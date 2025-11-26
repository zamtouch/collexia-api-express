<?php

require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../database.php';

/**
 * Student Controller
 * Handles student/tenant management
 */
class StudentController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create or update a student
     * POST /api/v1/students
     */
    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $required = ['student_id', 'full_name', 'email', 'account_number', 'bank_id'];
        $missing = Validator::required($input, $required);
        if ($missing !== true) {
            Response::error('Missing required fields: ' . implode(', ', $missing), 400);
        }
        
        // Validate email
        if (!Validator::email($input['email'])) {
            Response::error('Invalid email address', 400);
        }
        
        // Validate bank ID
        if (!Validator::bankId($input['bank_id'])) {
            Response::error('Invalid bank ID', 400);
        }
        
        // Validate account type
        $accountType = $input['account_type'] ?? 1;
        if (!Validator::accountType($accountType)) {
            Response::error('Invalid account type', 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO students (
                    student_id, full_name, email, phone, id_number, id_type,
                    account_number, account_type, bank_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    full_name = VALUES(full_name),
                    email = VALUES(email),
                    phone = VALUES(phone),
                    id_number = VALUES(id_number),
                    id_type = VALUES(id_type),
                    account_number = VALUES(account_number),
                    account_type = VALUES(account_type),
                    bank_id = VALUES(bank_id),
                    updated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                Validator::sanitize($input['student_id']),
                Validator::sanitize($input['full_name']),
                Validator::sanitize($input['email']),
                Validator::sanitize($input['phone'] ?? ''),
                Validator::sanitize($input['id_number'] ?? ''),
                (int)($input['id_type'] ?? 1), // 1=RSA ID, 2=Passport, 3=Temp ID, 4=Business
                Validator::sanitize($input['account_number']),
                (int)$accountType,
                (int)$input['bank_id']
            ]);
            
            $studentId = $this->db->lastInsertId();
            if ($studentId == 0) {
                // Student already exists, get ID
                $stmt = $this->db->prepare("SELECT id FROM students WHERE student_id = ?");
                $stmt->execute([$input['student_id']]);
                $student = $stmt->fetch();
                $studentId = $student['id'];
            }
            
            Response::success(['student_id' => $studentId], 'Student created/updated successfully', 201);
            
        } catch (Exception $e) {
            error_log("Student creation error: " . $e->getMessage());
            Response::error('Failed to create student: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get student by ID
     * GET /api/v1/students/:student_id
     */
    public function get($studentId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$studentId]);
            $student = $stmt->fetch();
            
            if (!$student) {
                Response::error('Student not found', 404);
            }
            
            // Remove sensitive data if needed
            unset($student['id']);
            
            Response::success($student);
            
        } catch (Exception $e) {
            error_log("Get student error: " . $e->getMessage());
            Response::error('Failed to get student: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all students
     * GET /api/v1/students
     */
    public function list() {
        try {
            $stmt = $this->db->query("
                SELECT student_id, full_name, email, phone, created_at, updated_at
                FROM students
                ORDER BY full_name
            ");
            $students = $stmt->fetchAll();
            
            Response::success($students);
            
        } catch (Exception $e) {
            error_log("List students error: " . $e->getMessage());
            Response::error('Failed to list students: ' . $e->getMessage(), 500);
        }
    }
}



