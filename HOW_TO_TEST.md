# How to Test the Express API

## 🚀 Quick Test Methods

### Method 1: Automated Test Script (Recommended)

Run the comprehensive test suite:

```bash
node test-endpoints.js
```

This tests all endpoints automatically and shows colored results.

### Method 2: Simple Test Script

Run the basic test:

```bash
npm test
```

Or:

```bash
node test-api.js
```

### Method 3: Manual Testing with cURL

#### Health Check
```bash
curl http://localhost:3000/api/v1/health
```

#### Create Student
```bash
curl -X POST http://localhost:3000/api/v1/students ^
  -H "Content-Type: application/json" ^
  -d "{\"student_id\":\"STU001\",\"full_name\":\"John Doe\",\"email\":\"john@example.com\",\"account_number\":\"123456789\",\"bank_id\":65}"
```

#### Get Student
```bash
curl http://localhost:3000/api/v1/students/STU001
```

#### List Students
```bash
curl http://localhost:3000/api/v1/students
```

#### Create Property
```bash
curl -X POST http://localhost:3000/api/v1/properties ^
  -H "Content-Type: application/json" ^
  -d "{\"property_code\":\"PROP001\",\"property_name\":\"Residence Block A\",\"monthly_rent\":5000}"
```

#### Register Mandate
```bash
curl -X POST http://localhost:3000/api/v1/mandates/register ^
  -H "Content-Type: application/json" ^
  -d "{\"student_id\":\"STU001\",\"property_id\":\"PROP001\",\"monthly_rent\":5000,\"start_date\":\"20250101\",\"frequency_code\":4,\"no_of_installments\":12}"
```

### Method 4: Using Postman

1. **Import Collection:**
   - Open Postman
   - Import → File → Select `Collexia_API.postman_collection.json`
   - Update base URL to `http://localhost:3000`

2. **Test Endpoints:**
   - Health Check: `GET /api/v1/health`
   - Create Student: `POST /api/v1/students`
   - Get Student: `GET /api/v1/students/:student_id`
   - Create Property: `POST /api/v1/properties`
   - Register Mandate: `POST /api/v1/mandates/register`

### Method 5: Browser Testing

Open in browser (GET requests only):

```
http://localhost:3000/api/v1/health
http://localhost:3000/api/v1/students
http://localhost:3000/api/v1/properties
```

## 📋 Test Checklist

### Basic Endpoints
- [ ] Health check returns 200
- [ ] Create student returns 201
- [ ] Get student returns 200
- [ ] List students returns 200
- [ ] Create property returns 201
- [ ] Get property returns 200
- [ ] List properties returns 200

### Mandate Endpoints
- [ ] Register mandate (may fail if Collexia API has issues)
- [ ] Check mandate status
- [ ] Get mandate by contract reference

### Payment Endpoints
- [ ] Download payments from Collexia
- [ ] Get payments by student
- [ ] Get payments by contract

## 🧪 Testing Collexia Integration

The API uses the **exact same Collexia credentials** as your PHP version:

- **Base URL:** `https://collection-uat.collexia.co`
- **Username:** `bareinvuat`
- **Client ID:** `6FA41D83-B8A5-11F0-B138-42010A960205`
- **Merchant GID:** `12584`
- **Remote GID:** `71`

### Test Collexia Connection

```bash
# This will call the actual Collexia API
curl -X POST http://localhost:3000/api/v1/mandates/register \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "STU001",
    "property_id": "PROP001",
    "monthly_rent": 5000.00,
    "start_date": "20250101",
    "frequency_code": 4,
    "no_of_installments": 12,
    "tracking_days": 3,
    "mag_id": 46
  }'
```

**Note:** This will make a real API call to Collexia UAT environment.

## 🔍 Debugging

### Check Server Logs

The server logs all requests. Watch the terminal where you ran `npm start`.

### Test Individual Components

```javascript
// Test Collexia client directly
const CollexiaClient = require('./api/utils/CollexiaClient');
const client = new CollexiaClient();

// Test headers
console.log(client.buildHeaders());
```

### Common Issues

1. **Port already in use:**
   ```bash
   # Change port
   PORT=3001 npm start
   ```

2. **CORS errors:**
   - Check allowed origins in `api/index.js`
   - Add your frontend URL to CORS config

3. **Collexia API errors:**
   - Check credentials in environment variables
   - Verify network connection
   - Check Collexia API status

## 📊 Expected Test Results

When you run `node test-endpoints.js`, you should see:

```
✅ Health Check - 200
✅ Create Student - 201
✅ Get Student - 200
✅ List Students - 200
✅ Create Property - 201
✅ Get Property - 200
✅ List Properties - 200
⚠️  Register Mandate - May fail if Collexia API has issues
```

## 🎯 Quick Test Commands

```bash
# Start server (if not running)
npm start

# Run full test suite
node test-endpoints.js

# Run simple test
npm test

# Test health endpoint
curl http://localhost:3000/api/v1/health

# Test in browser
# Open: http://localhost:3000/api/v1/health
```

## ✅ Success Indicators

- ✅ Server starts without errors
- ✅ Health endpoint returns `{"success": true}`
- ✅ Can create and retrieve students
- ✅ Can create and retrieve properties
- ✅ Collexia client builds headers correctly

---

**Your API is ready to test!** 🚀

Run `node test-endpoints.js` to see all tests in action.

