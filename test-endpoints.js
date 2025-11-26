// Comprehensive API Testing Script
const axios = require('axios');

const BASE_URL = process.env.API_URL || 'http://localhost:3000';
const colors = {
  green: '\x1b[32m',
  red: '\x1b[31m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m',
  reset: '\x1b[0m'
};

function log(message, color = 'reset') {
  console.log(`${colors[color]}${message}${colors.reset}`);
}

async function testEndpoint(name, method, url, data = null) {
  try {
    log(`\n📡 Testing: ${name}`, 'blue');
    log(`   ${method} ${url}`, 'yellow');
    
    const config = {
      method: method.toLowerCase(),
      url: `${BASE_URL}${url}`,
      headers: { 'Content-Type': 'application/json' }
    };
    
    if (data) {
      config.data = data;
      log(`   Body: ${JSON.stringify(data, null, 2)}`, 'yellow');
    }
    
    const response = await axios(config);
    log(`   ✅ Status: ${response.status}`, 'green');
    log(`   Response: ${JSON.stringify(response.data, null, 2)}`, 'green');
    return { success: true, data: response.data };
  } catch (error) {
    log(`   ❌ Error: ${error.response?.status || error.message}`, 'red');
    if (error.response?.data) {
      log(`   Response: ${JSON.stringify(error.response.data, null, 2)}`, 'red');
    }
    return { success: false, error: error.response?.data || error.message };
  }
}

async function runTests() {
  log('\n🧪 ========================================', 'blue');
  log('   Collexia Express API - Full Test Suite', 'blue');
  log('   ========================================\n', 'blue');
  
  const results = [];
  
  // 1. Health Check
  results.push(await testEndpoint('Health Check', 'GET', '/api/v1/health'));
  
  // 2. Create Student
  results.push(await testEndpoint('Create Student', 'POST', '/api/v1/students', {
    student_id: 'STU001',
    full_name: 'John Doe',
    email: 'john.doe@example.com',
    phone: '0812345678',
    id_number: '1234567890123',
    id_type: 1,
    account_number: '123456789',
    account_type: 1,
    bank_id: 65
  }));
  
  // 3. Get Student
  results.push(await testEndpoint('Get Student', 'GET', '/api/v1/students/STU001'));
  
  // 4. List Students
  results.push(await testEndpoint('List Students', 'GET', '/api/v1/students'));
  
  // 5. Create Property
  results.push(await testEndpoint('Create Property', 'POST', '/api/v1/properties', {
    property_code: 'PROP001',
    property_name: 'Student Residence Block A',
    address: '123 Main Street, Windhoek',
    monthly_rent: 5000.00
  }));
  
  // 6. Get Property
  results.push(await testEndpoint('Get Property', 'GET', '/api/v1/properties/PROP001'));
  
  // 7. List Properties
  results.push(await testEndpoint('List Properties', 'GET', '/api/v1/properties'));
  
  // 8. Register Mandate (requires student and property)
  results.push(await testEndpoint('Register Mandate', 'POST', '/api/v1/mandates/register', {
    student_id: 'STU001',
    property_id: 'PROP001',
    monthly_rent: 5000.00,
    start_date: '20250101',
    frequency_code: 4,
    no_of_installments: 12,
    tracking_days: 3,
    mag_id: 46
  }));
  
  // 9. Check Mandate Status (if mandate was created)
  const mandateResult = results[results.length - 1];
  if (mandateResult.success && mandateResult.data?.data?.contract_reference) {
    const contractRef = mandateResult.data.data.contract_reference;
    results.push(await testEndpoint('Check Mandate Status', 'POST', '/api/v1/mandates/status', {
      contract_reference: contractRef
    }));
  }
  
  // Summary
  const passed = results.filter(r => r.success).length;
  const failed = results.filter(r => !r.success).length;
  
  log('\n\n📊 ========================================', 'blue');
  log('   Test Summary', 'blue');
  log('   ========================================', 'blue');
  log(`   ✅ Passed: ${passed}`, 'green');
  log(`   ❌ Failed: ${failed}`, failed > 0 ? 'red' : 'green');
  log(`   📈 Total:  ${results.length}`, 'blue');
  log('   ========================================\n', 'blue');
  
  if (failed === 0) {
    log('🎉 All tests passed!', 'green');
  } else {
    log('⚠️  Some tests failed. Check errors above.', 'yellow');
    process.exit(1);
  }
}

// Run tests
runTests().catch(error => {
  log(`\n❌ Fatal error: ${error.message}`, 'red');
  process.exit(1);
});

