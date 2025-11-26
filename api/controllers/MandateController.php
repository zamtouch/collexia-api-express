<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/ContractReference.php';
require_once __DIR__ . '/../database.php';

/**
 * Mandate Controller
 * Handles mandate registration, status checks, and cancellation
 */
class MandateController {
    
    private $db;
    private $collexia;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->collexia = getCollexiaClient();
    }
    
    /**
     * Register a new mandate for rent payment
     * POST /api/v1/mandates/register
     */
    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['student_id', 'property_id', 'monthly_rent', 'start_date', 'frequency_code', 'no_of_installments'];
        $missing = Validator::required($input, $required);
        if ($missing !== true) {
            Response::error('Missing required fields: ' . implode(', ', $missing), 400);
        }
        
        try {
            $this->db->beginTransaction();
            
            // Get student information
            $stmt = $this->db->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$input['student_id']]);
            $student = $stmt->fetch();
            
            if (!$student) {
                Response::error('Student not found', 404);
            }
            
            // Get property information
            $stmt = $this->db->prepare("SELECT * FROM properties WHERE id = ?");
            $stmt->execute([$input['property_id']]);
            $property = $stmt->fetch();
            
            if (!$property) {
                Response::error('Property not found', 404);
            }
            
            // Get or create rental
            $stmt = $this->db->prepare("
                SELECT * FROM rentals 
                WHERE student_id = ? AND property_id = ? AND status = 'active'
            ");
            $stmt->execute([$student['id'], $property['id']]);
            $rental = $stmt->fetch();
            
            if (!$rental) {
                // Create new rental
                $contractRef = ContractReference::generate(
                    $GLOBALS['collexiaConfig']['merchant_gid'] ?? 0,
                    date('Ymd')
                );
                
                $stmt = $this->db->prepare("
                    INSERT INTO rentals (student_id, property_id, contract_reference, monthly_rent, start_date, status)
                    VALUES (?, ?, ?, ?, ?, 'active')
                ");
                $stmt->execute([
                    $student['id'],
                    $property['id'],
                    $contractRef,
                    $input['monthly_rent'],
                    $input['start_date']
                ]);
                $rentalId = $this->db->lastInsertId();
                $contractRef = $contractRef; // Use generated reference
            } else {
                $rentalId = $rental['id'];
                $contractRef = $rental['contract_reference'];
            }
            
            // Check if mandate already exists
            $stmt = $this->db->prepare("SELECT * FROM mandates WHERE contract_reference = ?");
            $stmt->execute([$contractRef]);
            $existingMandate = $stmt->fetch();
            
            if ($existingMandate && $existingMandate['mandate_loaded']) {
                Response::error('Mandate already registered for this rental', 409);
            }
            
            // Prepare Collexia mandate data
            global $collexiaConfig;
            $config = $GLOBALS['collexiaConfig'];
            $now = new DateTime();
            
            $messageInfo = [
                'merchantGid' => (int)$config['merchant_gid'],
                'remoteGid' => (int)$config['remote_gid'],
                'messageDate' => $now->format('Ymd'),
                'messageTime' => $now->format('His'),
                'systemUserName' => $config['basic_user'] ?? 'apiuser',
                'frontEndUserName' => $input['front_end_user_name'] ?? 'system'
            ];
            
            // Calculate collection day (day of month)
            $startDate = new DateTime($input['start_date']);
            $collectionDay = (int)$startDate->format('d');
            
            // For monthly, if day > 28, use 31 (last day of month)
            if ($input['frequency_code'] == 4 && $collectionDay > 28) {
                $collectionDay = 31;
            }
            
            $mandate = [
                'clientNo' => $student['student_id'],
                'userReference' => substr($contractRef, -6), // Last 6 chars of contract ref
                'frequencyCode' => (int)$input['frequency_code'],
                'installmentAmount' => (float)$input['monthly_rent'],
                'noOfInstallments' => (int)$input['no_of_installments'],
                'origin' => (int)($config['origin'] ?? $config['remote_gid'] ?? 0),
                'contractReference' => $contractRef,
                'magId' => $input['mag_id'] ?? 46, // Default to 46 (Endo)
                'initialAmount' => (float)($input['initial_amount'] ?? $input['monthly_rent']),
                'firstCollectionDate' => $input['start_date'],
                'collectionDay' => $collectionDay,
                'numberOfTrackingDays' => $input['tracking_days'] ?? 3,
                'debtorAccountName' => str_pad(substr($student['full_name'], 0, 35), 35, ' '),
                'debtorIdentificationType' => (int)$student['id_type'],
                'debtorIdentificationNo' => str_pad(substr($student['id_number'], 0, 33), 33, ' '),
                'debtorAccountNumber' => $student['account_number'],
                'debtorAccountType' => (int)$student['account_type'],
                'debtorBanId' => (int)$student['bank_id']
            ];
            
            $mandateData = [
                'messageInfo' => $messageInfo,
                'mandate' => $mandate
            ];
            
            // Call Collexia API
            $result = $this->collexia->loadMandate($mandateData, $contractRef);
            
            // Save mandate to database
            if ($result['ok']) {
                $stmt = $this->db->prepare("
                    INSERT INTO mandates (rental_id, contract_reference, user_reference, collexia_mandate_data)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE collexia_mandate_data = ?
                ");
                $stmt->execute([
                    $rentalId,
                    $contractRef,
                    $mandate['userReference'],
                    json_encode($result['data']),
                    json_encode($result['data'])
                ]);
                
                $this->db->commit();
                
                Response::success([
                    'contract_reference' => $contractRef,
                    'mandate_id' => $this->db->lastInsertId(),
                    'status' => 'registered'
                ], 'Mandate registered successfully');
            } else {
                $this->db->rollBack();
                Response::handleCollexiaResponse($result);
            }
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Mandate registration error: " . $e->getMessage());
            Response::error('Failed to register mandate: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Check mandate status
     * POST /api/v1/mandates/status
     */
    public function status() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['contract_reference'])) {
            Response::error('contract_reference is required', 400);
        }
        
        try {
            global $collexiaConfig;
            $config = $GLOBALS['collexiaConfig'];
            
            $payload = [
                'contractReference' => $input['contract_reference'],
                'merchantGid' => (int)$config['merchant_gid'],
                'frontEndUserName' => $input['front_end_user_name'] ?? 'system',
                'remoteGid' => (int)$config['remote_gid']
            ];
            
            $result = $this->collexia->mandateFinalFate($payload);
            
            if ($result['ok']) {
                // Update database
                $data = $result['data'];
                $stmt = $this->db->prepare("
                    UPDATE mandates 
                    SET mandate_loaded = ?, response_code = ?, authorization_outstanding = ?, status = ?
                    WHERE contract_reference = ?
                ");
                $stmt->execute([
                    $data['mandateLoaded'] ?? false,
                    $data['mandateLoadedResponseCode'] ?? null,
                    $data['authorizationOutstanding'] ?? 1,
                    $data['mandateLoaded'] ? 2 : 1, // 1=Data Error, 2=Active
                    $input['contract_reference']
                ]);
            }
            
            Response::handleCollexiaResponse($result);
            
        } catch (Exception $e) {
            error_log("Mandate status check error: " . $e->getMessage());
            Response::error('Failed to check mandate status: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Cancel a mandate
     * POST /api/v1/mandates/cancel
     */
    public function cancel() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['contract_reference'])) {
            Response::error('contract_reference is required', 400);
        }
        
        try {
            global $collexiaConfig;
            $config = $GLOBALS['collexiaConfig'];
            
            $payload = [
                'contractReference' => $input['contract_reference'],
                'frontEndUserName' => $input['front_end_user_name'] ?? 'system',
                'remoteGid' => (int)$config['remote_gid'],
                'merchantGid' => (int)$config['merchant_gid']
            ];
            
            $result = $this->collexia->post('/mandates/cancel', $payload);
            
            if ($result['ok']) {
                // Update database
                $stmt = $this->db->prepare("
                    UPDATE mandates SET status = 4 WHERE contract_reference = ?
                ");
                $stmt->execute([$input['contract_reference']]);
                
                $stmt = $this->db->prepare("
                    UPDATE rentals SET status = 'cancelled' WHERE contract_reference = ?
                ");
                $stmt->execute([$input['contract_reference']]);
            }
            
            Response::handleCollexiaResponse($result);
            
        } catch (Exception $e) {
            error_log("Mandate cancellation error: " . $e->getMessage());
            Response::error('Failed to cancel mandate: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get mandate details
     * GET /api/v1/mandates/:contract_reference
     */
    public function get($contractReference) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, r.monthly_rent, r.start_date, r.end_date, r.status as rental_status,
                       s.full_name as student_name, s.email, s.phone,
                       p.property_name, p.property_code
                FROM mandates m
                JOIN rentals r ON m.rental_id = r.id
                JOIN students s ON r.student_id = s.id
                JOIN properties p ON r.property_id = p.id
                WHERE m.contract_reference = ?
            ");
            $stmt->execute([$contractReference]);
            $mandate = $stmt->fetch();
            
            if (!$mandate) {
                Response::error('Mandate not found', 404);
            }
            
            Response::success($mandate);
            
        } catch (Exception $e) {
            error_log("Get mandate error: " . $e->getMessage());
            Response::error('Failed to get mandate: ' . $e->getMessage(), 500);
        }
    }
}

