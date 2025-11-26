<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../database.php';

/**
 * Payment Controller
 * Handles payment history and status
 */
class PaymentController {
    
    private $db;
    private $collexia;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->collexia = getCollexiaClient();
    }
    
    /**
     * Download payment history from Collexia
     * POST /api/v1/payments/download
     */
    public function download() {
        try {
            global $collexiaConfig;
            $config = $GLOBALS['collexiaConfig'];
            
            $payload = [
                'merchantGid' => (int)$config['merchant_gid'],
                'frontEndUserName' => $config['basic_user'] ?? 'apiuser',
                'remoteGid' => (int)$config['remote_gid']
            ];
            
            $result = $this->collexia->downloadPayments($payload);
            
            if ($result['ok'] && isset($result['data']['responses'])) {
                // Process and store payments
                $this->processPayments($result['data']['responses']);
            }
            
            Response::handleCollexiaResponse($result);
            
        } catch (Exception $e) {
            error_log("Payment download error: " . $e->getMessage());
            Response::error('Failed to download payments: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Process and store payment responses
     */
    private function processPayments($responses) {
        foreach ($responses as $payment) {
            try {
                // Find mandate by contract reference
                $stmt = $this->db->prepare("SELECT id FROM mandates WHERE contract_reference = ?");
                $stmt->execute([$payment['contractReference']]);
                $mandate = $stmt->fetch();
                
                if ($mandate) {
                    // Check if payment already exists
                    $stmt = $this->db->prepare("
                        SELECT id FROM payments 
                        WHERE mandate_id = ? AND installment_no = ? AND id = ?
                    ");
                    $stmt->execute([
                        $mandate['id'],
                        $payment['installmentNo'],
                        $payment['id']
                    ]);
                    $existing = $stmt->fetch();
                    
                    if (!$existing) {
                        // Insert new payment record
                        $stmt = $this->db->prepare("
                            INSERT INTO payments (
                                mandate_id, installment_no, scheduled_date, payment_date,
                                amount, amount_paid, status, response_code, response_description,
                                collexia_payment_data
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        $status = $this->mapPaymentStatus($payment['responseCode']);
                        
                        $stmt->execute([
                            $mandate['id'],
                            $payment['installmentNo'],
                            $payment['paymentDate'] ?? null,
                            $payment['paymentDate'] ?? null,
                            $payment['paymentAmount'] ?? 0,
                            $payment['paymentAmount'] ?? 0,
                            $status,
                            $payment['responseCode'] ?? null,
                            $payment['responseDescription'] ?? null,
                            json_encode($payment)
                        ]);
                    }
                }
            } catch (Exception $e) {
                error_log("Error processing payment: " . $e->getMessage());
                // Continue with next payment
            }
        }
    }
    
    /**
     * Map Collexia response code to payment status
     */
    private function mapPaymentStatus($responseCode) {
        // Map response codes to status
        $statusMap = [
            '0' => 'completed',
            '02' => 'rejected',
            '03' => 'rejected',
            '06' => 'rejected',
            '12' => 'rejected',
            '30' => 'rejected',
            '48' => 'rejected',
            '99' => 'tracking'
        ];
        
        return $statusMap[$responseCode] ?? 'pending';
    }
    
    /**
     * Get payment history for a student
     * GET /api/v1/payments/student/:student_id
     */
    public function getByStudent($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, m.contract_reference, r.monthly_rent,
                       pr.property_name, pr.property_code
                FROM payments p
                JOIN mandates m ON p.mandate_id = m.id
                JOIN rentals r ON m.rental_id = r.id
                JOIN properties pr ON r.property_id = pr.id
                JOIN students s ON r.student_id = s.id
                WHERE s.student_id = ?
                ORDER BY p.scheduled_date DESC
            ");
            $stmt->execute([$studentId]);
            $payments = $stmt->fetchAll();
            
            Response::success($payments);
            
        } catch (Exception $e) {
            error_log("Get payments error: " . $e->getMessage());
            Response::error('Failed to get payments: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get payment history for a contract
     * GET /api/v1/payments/contract/:contract_reference
     */
    public function getByContract($contractReference) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, m.contract_reference
                FROM payments p
                JOIN mandates m ON p.mandate_id = m.id
                WHERE m.contract_reference = ?
                ORDER BY p.scheduled_date DESC
            ");
            $stmt->execute([$contractReference]);
            $payments = $stmt->fetchAll();
            
            Response::success($payments);
            
        } catch (Exception $e) {
            error_log("Get payments error: " . $e->getMessage());
            Response::error('Failed to get payments: ' . $e->getMessage(), 500);
        }
    }
}

