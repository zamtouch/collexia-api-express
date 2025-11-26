const express = require('express');
const router = express.Router();
const db = require('../config/database');
const { validateStudent, validateEmail, validateBankId } = require('../utils/validator');

// Get all students
router.get('/', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [students] = await pool.execute(`
      SELECT student_id, full_name, email, phone, created_at, updated_at
      FROM students
      ORDER BY full_name
    `);
    
    res.json({
      success: true,
      message: 'Success',
      data: students,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

// Get student by ID
router.get('/:student_id', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [students] = await pool.execute(
      'SELECT * FROM students WHERE student_id = ?',
      [req.params.student_id]
    );
    
    if (students.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Student not found',
        timestamp: new Date().toISOString()
      });
    }
    
    const student = students[0];
    delete student.id; // Remove internal ID
    
    res.json({
      success: true,
      message: 'Success',
      data: student,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

// Create or update student
router.post('/', async (req, res, next) => {
  try {
    const input = req.body;
    
    // Validate required fields
    const required = ['student_id', 'full_name', 'email', 'account_number', 'bank_id'];
    const missing = required.filter(field => !input[field]);
    
    if (missing.length > 0) {
      return res.status(400).json({
        success: false,
        message: `Missing required fields: ${missing.join(', ')}`,
        timestamp: new Date().toISOString()
      });
    }
    
    // Validate email
    if (!validateEmail(input.email)) {
      return res.status(400).json({
        success: false,
        message: 'Invalid email address',
        timestamp: new Date().toISOString()
      });
    }
    
    // Validate bank ID
    if (!validateBankId(input.bank_id)) {
      return res.status(400).json({
        success: false,
        message: 'Invalid bank ID',
        timestamp: new Date().toISOString()
      });
    }
    
    const pool = await db.getConnection();
    
    await pool.execute(`
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
    `, [
      input.student_id,
      input.full_name,
      input.email,
      input.phone || '',
      input.id_number || '',
      input.id_type || 1,
      input.account_number,
      input.account_type || 1,
      input.bank_id
    ]);
    
    res.status(201).json({
      success: true,
      message: 'Student created/updated successfully',
      data: { student_id: input.student_id },
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;

