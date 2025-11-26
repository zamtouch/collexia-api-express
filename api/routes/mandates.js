const express = require('express');
const router = express.Router();
const db = require('../config/database');
const CollexiaClient = require('../utils/CollexiaClient');
const { generateContractReference } = require('../utils/contractReference');

// Register mandate
router.post('/register', async (req, res, next) => {
  try {
    const input = req.body;
    const required = ['student_id', 'property_id', 'monthly_rent', 'start_date', 'frequency_code', 'no_of_installments'];
    const missing = required.filter(field => !input[field]);
    
    if (missing.length > 0) {
      return res.status(400).json({
        success: false,
        message: `Missing required fields: ${missing.join(', ')}`,
        timestamp: new Date().toISOString()
      });
    }
    
    const pool = await db.getConnection();
    const connection = await pool.getConnection();
    
    try {
      await connection.beginTransaction();
      
      // Get student
      const [students] = await connection.execute(
        'SELECT * FROM students WHERE student_id = ?',
        [input.student_id]
      );
      if (students.length === 0) {
        await connection.rollback();
        return res.status(404).json({
          success: false,
          message: 'Student not found',
          timestamp: new Date().toISOString()
        });
      }
      const student = students[0];
      
      // Get property
      const [properties] = await connection.execute(
        'SELECT * FROM properties WHERE id = ?',
        [input.property_id]
      );
      if (properties.length === 0) {
        await connection.rollback();
        return res.status(404).json({
          success: false,
          message: 'Property not found',
          timestamp: new Date().toISOString()
        });
      }
      const property = properties[0];
      
      // Get or create rental
      let [rentals] = await connection.execute(
        'SELECT * FROM rentals WHERE student_id = ? AND property_id = ? AND status = ?',
        [student.id, property.id, 'active']
      );
      
      let rental;
      if (rentals.length === 0) {
        const contractRef = generateContractReference(
          process.env.COLLEXIA_MERCHANT_GID || 12584,
          new Date().toISOString().split('T')[0].replace(/-/g, '')
        );
        
        await connection.execute(
          'INSERT INTO rentals (student_id, property_id, contract_reference, monthly_rent, start_date, status) VALUES (?, ?, ?, ?, ?, ?)',
          [student.id, property.id, contractRef, input.monthly_rent, input.start_date, 'active']
        );
        
        [rentals] = await connection.execute(
          'SELECT * FROM rentals WHERE contract_reference = ?',
          [contractRef]
        );
      }
      rental = rentals[0];
      
      // Check existing mandate
      const [mandates] = await connection.execute(
        'SELECT * FROM mandates WHERE contract_reference = ?',
        [rental.contract_reference]
      );
      
      if (mandates.length > 0 && mandates[0].mandate_loaded) {
        await connection.rollback();
        return res.status(409).json({
          success: false,
          message: 'Mandate already registered for this rental',
          timestamp: new Date().toISOString()
        });
      }
      
      // Prepare Collexia payload
      const now = new Date();
      const messageDate = now.toISOString().split('T')[0].replace(/-/g, '');
      const messageTime = now.toTimeString().split(' ')[0].replace(/:/g, '');
      
      const collexiaPayload = {
        messageInfo: {
          merchantGid: parseInt(process.env.COLLEXIA_MERCHANT_GID || 12584),
          remoteGid: parseInt(process.env.COLLEXIA_REMOTE_GID || 71),
          messageDate,
          messageTime,
          systemUserName: process.env.COLLEXIA_BASIC_USER || '',
          frontEndUserName: 'nodejs'
        },
        mandate: {
          clientNo: student.student_id,
          userReference: rental.contract_reference.substring(0, 10),
          frequencyCode: parseInt(input.frequency_code),
          installmentAmount: parseFloat(input.monthly_rent),
          noOfInstallments: parseInt(input.no_of_installments),
          origin: parseInt(process.env.COLLEXIA_REMOTE_GID || 71),
          contractReference: rental.contract_reference,
          magId: parseInt(input.mag_id || 46),
          initialAmount: 0.00,
          firstCollectionDate: input.start_date,
          collectionDay: parseInt(input.start_date.substring(6, 8)),
          numberOfTrackingDays: parseInt(input.tracking_days || 3),
          debtorAccountName: student.full_name,
          debtorIdentificationType: student.id_type || 1,
          debtorIdentificationNo: student.id_number || '',
          debtorAccountNumber: student.account_number,
          debtorAccountType: student.account_type || 1,
          debtorBanId: student.bank_id
        }
      };
      
      // Call Collexia API
      const collexiaClient = new CollexiaClient();
      const result = await collexiaClient.loadMandate(collexiaPayload, rental.contract_reference);
      
      // Save mandate
      if (mandates.length > 0) {
        await connection.execute(
          'UPDATE mandates SET status = ?, mandate_loaded = ?, response_code = ?, collexia_mandate_data = ? WHERE contract_reference = ?',
          [
            result.ok ? 1 : 0,
            result.ok,
            result.data?.responseCode || '',
            JSON.stringify(result.data || {}),
            rental.contract_reference
          ]
        );
      } else {
        await connection.execute(
          'INSERT INTO mandates (rental_id, contract_reference, user_reference, status, mandate_loaded, response_code, collexia_mandate_data) VALUES (?, ?, ?, ?, ?, ?, ?)',
          [
            rental.id,
            rental.contract_reference,
            rental.contract_reference.substring(0, 10),
            result.ok ? 1 : 0,
            result.ok,
            result.data?.responseCode || '',
            JSON.stringify(result.data || {})
          ]
        );
      }
      
      await connection.commit();
      
      if (result.ok) {
        res.status(201).json({
          success: true,
          message: 'Mandate registered successfully',
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
      await connection.rollback();
      throw error;
    } finally {
      connection.release();
    }
  } catch (error) {
    next(error);
  }
});

// Check mandate status
router.post('/status', async (req, res, next) => {
  try {
    const { contract_reference } = req.body;
    
    if (!contract_reference) {
      return res.status(400).json({
        success: false,
        message: 'contract_reference is required',
        timestamp: new Date().toISOString()
      });
    }
    
    const collexiaClient = new CollexiaClient();
    const result = await collexiaClient.checkMandateStatus(contract_reference);
    
    if (result.ok) {
      res.json({
        success: true,
        message: 'Success',
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

// Get mandate by contract reference
router.get('/:contract_reference', async (req, res, next) => {
  try {
    const pool = await db.getConnection();
    const [mandates] = await pool.execute(
      'SELECT * FROM mandates WHERE contract_reference = ?',
      [req.params.contract_reference]
    );
    
    if (mandates.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Mandate not found',
        timestamp: new Date().toISOString()
      });
    }
    
    res.json({
      success: true,
      message: 'Success',
      data: mandates[0],
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;

