// Quick test script for the Express API
const axios = require('axios');

const BASE_URL = process.env.API_URL || 'http://localhost:3000';

async function test() {
  console.log('🧪 Testing Collexia Express API\n');
  console.log(`Base URL: ${BASE_URL}\n`);
  
  try {
    // 1. Health check
    console.log('1. Health Check...');
    const health = await axios.get(`${BASE_URL}/api/v1/health`);
    console.log('✅', health.data);
    console.log('');
    
    // 2. Create student
    console.log('2. Creating Student...');
    const student = await axios.post(`${BASE_URL}/api/v1/students`, {
      student_id: 'STU001',
      full_name: 'John Doe',
      email: 'john@example.com',
      phone: '0812345678',
      id_number: '1234567890123',
      id_type: 1,
      account_number: '123456789',
      account_type: 1,
      bank_id: 65
    });
    console.log('✅', student.data);
    console.log('');
    
    // 3. Get student
    console.log('3. Getting Student...');
    const getStudent = await axios.get(`${BASE_URL}/api/v1/students/STU001`);
    console.log('✅', getStudent.data);
    console.log('');
    
    // 4. List students
    console.log('4. Listing Students...');
    const students = await axios.get(`${BASE_URL}/api/v1/students`);
    console.log('✅', students.data);
    console.log('');
    
    // 5. Create property
    console.log('5. Creating Property...');
    const property = await axios.post(`${BASE_URL}/api/v1/properties`, {
      property_code: 'PROP001',
      property_name: 'Student Residence Block A',
      address: '123 Main Street, Windhoek',
      monthly_rent: 5000.00
    });
    console.log('✅', property.data);
    console.log('');
    
    // 6. List properties
    console.log('6. Listing Properties...');
    const properties = await axios.get(`${BASE_URL}/api/v1/properties`);
    console.log('✅', properties.data);
    console.log('');
    
    console.log('✅ All tests passed!');
    
  } catch (error) {
    console.error('❌ Test failed:', error.response?.data || error.message);
    process.exit(1);
  }
}

test();

