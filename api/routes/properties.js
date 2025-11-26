const express = require('express');
const router = express.Router();
const db = require('../config/database');

// Get all properties
router.get('/', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [properties] = await pool.execute(`
      SELECT * FROM properties
      ORDER BY property_name
    `);
    
    res.json({
      success: true,
      message: 'Success',
      data: properties,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

// Get property by code
router.get('/:property_code', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [properties] = await pool.execute(
      'SELECT * FROM properties WHERE property_code = ?',
      [req.params.property_code]
    );
    
    if (properties.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Property not found',
        timestamp: new Date().toISOString()
      });
    }
    
    res.json({
      success: true,
      message: 'Success',
      data: properties[0],
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

// Create or update property
router.post('/', async (req, res, next) => {
  try {
    const input = req.body;
    
    const required = ['property_code', 'property_name', 'monthly_rent'];
    const missing = required.filter(field => !input[field]);
    
    if (missing.length > 0) {
      return res.status(400).json({
        success: false,
        message: `Missing required fields: ${missing.join(', ')}`,
        timestamp: new Date().toISOString()
      });
    }
    
    const pool = await db.getConnection();
    
    await pool.execute(`
      INSERT INTO properties (property_code, property_name, address, monthly_rent)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE
        property_name = VALUES(property_name),
        address = VALUES(address),
        monthly_rent = VALUES(monthly_rent),
        updated_at = CURRENT_TIMESTAMP
    `, [
      input.property_code,
      input.property_name,
      input.address || '',
      input.monthly_rent
    ]);
    
    res.status(201).json({
      success: true,
      message: 'Property created/updated successfully',
      data: { property_code: input.property_code },
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;

