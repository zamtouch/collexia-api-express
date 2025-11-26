const mysql = require('mysql2/promise');

class Database {
  constructor() {
    this.pool = null;
  }

  async connect() {
    if (this.pool) {
      return this.pool;
    }

    this.pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || '',
      database: process.env.DB_NAME || 'collexia_rentals',
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0,
      charset: 'utf8mb4'
    });

    return this.pool;
  }

  async getConnection() {
    await this.connect();
    return this.pool;
  }

  async initializeTables() {
    const pool = await this.getConnection();
    
    const sql = `
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
    `;

    // Split and execute each statement
    const statements = sql.split(';').filter(s => s.trim().length > 0);
    for (const statement of statements) {
      if (statement.trim()) {
        await pool.execute(statement);
      }
    }
  }
}

// Singleton instance
const db = new Database();

module.exports = db;

