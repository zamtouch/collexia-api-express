<?php

require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../database.php';

/**
 * Property Controller
 * Handles property management
 */
class PropertyController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create or update a property
     * POST /api/v1/properties
     */
    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $required = ['property_code', 'property_name', 'monthly_rent'];
        $missing = Validator::required($input, $required);
        if ($missing !== true) {
            Response::error('Missing required fields: ' . implode(', ', $missing), 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO properties (property_code, property_name, address, monthly_rent)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    property_name = VALUES(property_name),
                    address = VALUES(address),
                    monthly_rent = VALUES(monthly_rent),
                    updated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                Validator::sanitize($input['property_code']),
                Validator::sanitize($input['property_name']),
                Validator::sanitize($input['address'] ?? ''),
                (float)$input['monthly_rent']
            ]);
            
            $propertyId = $this->db->lastInsertId();
            if ($propertyId == 0) {
                $stmt = $this->db->prepare("SELECT id FROM properties WHERE property_code = ?");
                $stmt->execute([$input['property_code']]);
                $property = $stmt->fetch();
                $propertyId = $property['id'];
            }
            
            Response::success(['property_id' => $propertyId], 'Property created/updated successfully', 201);
            
        } catch (Exception $e) {
            error_log("Property creation error: " . $e->getMessage());
            Response::error('Failed to create property: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get property by code
     * GET /api/v1/properties/:property_code
     */
    public function get($propertyCode) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM properties WHERE property_code = ?");
            $stmt->execute([$propertyCode]);
            $property = $stmt->fetch();
            
            if (!$property) {
                Response::error('Property not found', 404);
            }
            
            Response::success($property);
            
        } catch (Exception $e) {
            error_log("Get property error: " . $e->getMessage());
            Response::error('Failed to get property: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all properties
     * GET /api/v1/properties
     */
    public function list() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM properties
                ORDER BY property_name
            ");
            $properties = $stmt->fetchAll();
            
            Response::success($properties);
            
        } catch (Exception $e) {
            error_log("List properties error: " . $e->getMessage());
            Response::error('Failed to list properties: ' . $e->getMessage(), 500);
        }
    }
}



