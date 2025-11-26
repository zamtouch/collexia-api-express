const express = require('express');
const cors = require('cors');
const app = express();

// Load environment variables
require('dotenv').config();

// Middleware
app.use(cors({
  origin: [
    'http://localhost:3000',
    'http://localhost:3001',
    'exp://localhost:8081',
    'https://app.pozi.com.na',
    'https://www.pozi.com.na'
  ],
  credentials: true
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// In-memory storage (no database needed for testing)
const storage = {
  students: [],
  properties: [],
  rentals: [],
  mandates: [],
  payments: []
};

// Health check
app.get('/api/v1/health', (req, res) => {
  res.json({
    success: true,
    message: 'API is running',
    timestamp: new Date().toISOString()
  });
});

// Student routes
app.get('/api/v1/students', (req, res) => {
  res.json({
    success: true,
    message: 'Success',
    data: storage.students.map(s => ({
      student_id: s.student_id,
      full_name: s.full_name,
      email: s.email,
      phone: s.phone,
      created_at: s.created_at,
      updated_at: s.updated_at
    })),
    timestamp: new Date().toISOString()
  });
});

app.get('/api/v1/students/:student_id', (req, res) => {
  const student = storage.students.find(s => s.student_id === req.params.student_id);
  if (!student) {
    return res.status(404).json({
      success: false,
      message: 'Student not found',
      timestamp: new Date().toISOString()
    });
  }
  res.json({
    success: true,
    message: 'Success',
    data: student,
    timestamp: new Date().toISOString()
  });
});

app.post('/api/v1/students', (req, res) => {
  const input = req.body;
  const required = ['student_id', 'full_name', 'email', 'account_number', 'bank_id'];
  const missing = required.filter(field => !input[field]);
  
  if (missing.length > 0) {
    return res.status(400).json({
      success: false,
      message: `Missing required fields: ${missing.join(', ')}`,
      timestamp: new Date().toISOString()
    });
  }
  
  const existingIndex = storage.students.findIndex(s => s.student_id === input.student_id);
  const studentData = {
    ...input,
    created_at: existingIndex === -1 ? new Date().toISOString() : storage.students[existingIndex].created_at,
    updated_at: new Date().toISOString()
  };
  
  if (existingIndex === -1) {
    storage.students.push(studentData);
  } else {
    storage.students[existingIndex] = studentData;
  }
  
  res.status(201).json({
    success: true,
    message: 'Student created/updated successfully',
    data: { student_id: input.student_id },
    timestamp: new Date().toISOString()
  });
});

// Property routes
app.get('/api/v1/properties', (req, res) => {
  res.json({
    success: true,
    message: 'Success',
    data: storage.properties,
    timestamp: new Date().toISOString()
  });
});

app.get('/api/v1/properties/:property_code', (req, res) => {
  const property = storage.properties.find(p => p.property_code === req.params.property_code);
  if (!property) {
    return res.status(404).json({
      success: false,
      message: 'Property not found',
      timestamp: new Date().toISOString()
    });
  }
  res.json({
    success: true,
    message: 'Success',
    data: property,
    timestamp: new Date().toISOString()
  });
});

app.post('/api/v1/properties', (req, res) => {
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
  
  const existingIndex = storage.properties.findIndex(p => p.property_code === input.property_code);
  const propertyData = {
    ...input,
    created_at: existingIndex === -1 ? new Date().toISOString() : storage.properties[existingIndex].created_at,
    updated_at: new Date().toISOString()
  };
  
  if (existingIndex === -1) {
    storage.properties.push(propertyData);
  } else {
    storage.properties[existingIndex] = propertyData;
  }
  
  res.status(201).json({
    success: true,
    message: 'Property created/updated successfully',
    data: { property_code: input.property_code },
    timestamp: new Date().toISOString()
  });
});

// Mandate routes
const CollexiaClient = require('./utils/CollexiaClient');
const { generateContractReference } = require('./utils/contractReference');

app.post('/api/v1/mandates/register', async (req, res) => {
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
    
    const student = storage.students.find(s => s.student_id === input.student_id);
    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Student not found',
        timestamp: new Date().toISOString()
      });
    }
    
    const property = storage.properties.find(p => p.id === input.property_id || p.property_code === input.property_id);
    if (!property) {
      return res.status(404).json({
        success: false,
        message: 'Property not found',
        timestamp: new Date().toISOString()
      });
    }
    
    const contractRef = generateContractReference(
      process.env.COLLEXIA_MERCHANT_GID || 12584,
      new Date().toISOString().split('T')[0].replace(/-/g, '')
    );
    
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
        systemUserName: process.env.COLLEXIA_BASIC_USER || 'bareinvuat',
        frontEndUserName: 'nodejs'
      },
      mandate: {
        clientNo: student.student_id,
        userReference: contractRef.substring(0, 10),
        frequencyCode: parseInt(input.frequency_code),
        installmentAmount: parseFloat(input.monthly_rent),
        noOfInstallments: parseInt(input.no_of_installments),
        origin: parseInt(process.env.COLLEXIA_REMOTE_GID || 71),
        contractReference: contractRef,
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
    const result = await collexiaClient.loadMandate(collexiaPayload, contractRef);
    
    // Save mandate
    const mandate = {
      contract_reference: contractRef,
      student_id: input.student_id,
      property_id: input.property_id,
      monthly_rent: input.monthly_rent,
      status: result.ok ? 1 : 0,
      mandate_loaded: result.ok,
      response_code: result.data?.responseCode || '',
      collexia_data: result.data || {},
      created_at: new Date().toISOString()
    };
    
    storage.mandates.push(mandate);
    
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
    res.status(500).json({
      success: false,
      message: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

app.post('/api/v1/mandates/status', async (req, res) => {
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
    res.status(500).json({
      success: false,
      message: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

app.get('/api/v1/mandates/:contract_reference', (req, res) => {
  const mandate = storage.mandates.find(m => m.contract_reference === req.params.contract_reference);
  if (!mandate) {
    return res.status(404).json({
      success: false,
      message: 'Mandate not found',
      timestamp: new Date().toISOString()
    });
  }
  res.json({
    success: true,
    message: 'Success',
    data: mandate,
    timestamp: new Date().toISOString()
  });
});

// Payment routes
app.post('/api/v1/payments/download', async (req, res) => {
  try {
    const collexiaClient = new CollexiaClient();
    const result = await collexiaClient.downloadPayments();
    
    if (result.ok) {
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
    res.status(500).json({
      success: false,
      message: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

app.get('/api/v1/payments/student/:student_id', (req, res) => {
  const payments = storage.payments.filter(p => p.student_id === req.params.student_id);
  res.json({
    success: true,
    message: 'Success',
    data: payments,
    timestamp: new Date().toISOString()
  });
});

app.get('/api/v1/payments/contract/:contract_reference', (req, res) => {
  const payments = storage.payments.filter(p => p.contract_reference === req.params.contract_reference);
  res.json({
    success: true,
    message: 'Success',
    data: payments,
    timestamp: new Date().toISOString()
  });
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: 'Endpoint not found',
    path: req.path
  });
});

// Error handler
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(err.status || 500).json({
    success: false,
    message: err.message || 'Internal server error',
    timestamp: new Date().toISOString()
  });
});

// For Vercel serverless
module.exports = app;

// For local development
if (require.main === module) {
  const PORT = process.env.PORT || 3000;
  app.listen(PORT, () => {
    console.log(`🚀 Server running on http://localhost:${PORT}`);
    console.log(`📡 Health: http://localhost:${PORT}/api/v1/health`);
    console.log(`📚 API Base: http://localhost:${PORT}/api/v1`);
  });
}

