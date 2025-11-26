const express = require('express');
const router = express.Router();
const db = require('../config/database');
const CollexiaClient = require('../utils/CollexiaClient');

// Download payment history from Collexia
router.post('/download', async (req, res, next) => {
  try {
    const collexiaClient = new CollexiaClient();
    const result = await collexiaClient.downloadPayments();
    
    if (result.ok) {
      // Process and save payments to database
      const pool = await db.getConnection();
      // Implementation depends on Collexia response format
      
      res.json({
        success: true,
        message: 'Payments downloaded successfully',
        data: result.data,
        timestamp: new Date().toISOString()
      });
    } else {
      res.status(500).json({
        success: false,
        message: 'Collexia API error',
        errors: result.error,
        timestamp: new Date().toISOString()
      });
    }
  } catch (error) {
    next(error);
  }
});

// Get payments by student
router.get('/student/:student_id', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [payments] = await pool.execute(`
      SELECT p.* FROM payments p
      INNER JOIN mandates m ON p.mandate_id = m.id
      INNER JOIN rentals r ON m.rental_id = r.id
      INNER JOIN students s ON r.student_id = s.id
      WHERE s.student_id = ?
      ORDER BY p.scheduled_date DESC
    `, [req.params.student_id]);
    
    res.json({
      success: true,
      message: 'Success',
      data: payments,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

// Get payments by contract
router.get('/contract/:contract_reference', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [payments] = await pool.execute(`
      SELECT p.* FROM payments p
      INNER JOIN mandates m ON p.mandate_id = m.id
      WHERE m.contract_reference = ?
      ORDER BY p.scheduled_date DESC
    `, [req.params.contract_reference]);
    
    res.json({
      success: true,
      message: 'Success',
      data: payments,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;

