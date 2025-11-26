<?php

/**
 * Database connection and helper functions
 */
class Database {
    private static $instance = null;
    private $conn;
    
    // Use environment variables for cPanel deployment, fallback to defaults for local dev
    private $host;
    private $dbname;
    private $username;
    private $password;
    
    private function __construct() {
        // Load from environment variables (cPanel) or use defaults (local dev)
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'collexia_rentals';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Initialize database tables
     */
    public function initializeTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(50) UNIQUE NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            id_number VARCHAR(50),
            id_type INT DEFAULT 1,
            account_number VARCHAR(20),
            account_type INT DEFAULT 1,
            bank_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_student_id (student_id),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_code VARCHAR(50) UNIQUE NOT NULL,
            property_name VARCHAR(255) NOT NULL,
            address TEXT,
            monthly_rent DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_property_code (property_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS rentals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            property_id INT NOT NULL,
            contract_reference VARCHAR(14) UNIQUE NOT NULL,
            monthly_rent DECIMAL(10,2) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE,
            status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            INDEX idx_contract_reference (contract_reference),
            INDEX idx_student_id (student_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS mandates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            rental_id INT NOT NULL,
            contract_reference VARCHAR(14) UNIQUE NOT NULL,
            user_reference VARCHAR(10) NOT NULL,
            status INT DEFAULT 1,
            mandate_loaded BOOLEAN DEFAULT FALSE,
            response_code VARCHAR(10),
            authorization_outstanding INT DEFAULT 1,
            collexia_mandate_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE,
            INDEX idx_contract_reference (contract_reference),
            INDEX idx_rental_id (rental_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mandate_id INT NOT NULL,
            installment_no INT NOT NULL,
            scheduled_date DATE NOT NULL,
            payment_date DATE,
            amount DECIMAL(10,2) NOT NULL,
            amount_paid DECIMAL(10,2) DEFAULT 0,
            status VARCHAR(50) DEFAULT 'pending',
            response_code VARCHAR(10),
            response_description TEXT,
            collexia_payment_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (mandate_id) REFERENCES mandates(id) ON DELETE CASCADE,
            INDEX idx_mandate_id (mandate_id),
            INDEX idx_scheduled_date (scheduled_date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $this->conn->exec($sql);
    }
}

