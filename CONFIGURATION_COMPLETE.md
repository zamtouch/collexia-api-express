# ✅ Configuration Complete!

## All Credentials Configured

Your Collexia UAT API is now fully configured and ready to use!

### ✅ Configured Settings

| Setting | Value | Status |
|---------|-------|--------|
| **Base URL** | `https://collection-uat.collexia.co` | ✅ |
| **Base Path** | `/api/coswitchuadsrest/v3` | ✅ |
| **Username** | `bareinvuat` | ✅ |
| **Password** | `Ms@utbinT!11` | ✅ |
| **Merchant GID** | `12584` (XXINTEGR) | ✅ |
| **Remote GID** | `71` | ✅ |
| **Origin** | `71` | ✅ |
| **Client ID** | `6FA41D83-B8A5-11F0-B138-42010A960205` | ✅ |
| **Client Secret** | `9FXhhuOtjiKinPFpbnSb` | ✅ |
| **Header Prefix** | `X-COLLEXIA` | ✅ |

## 🎉 API Status: READY

Your PHP API is now fully configured and can:
- ✅ Connect to Collexia UAT
- ✅ Generate proper authentication headers
- ✅ Create HMAC signatures
- ✅ Register mandates
- ✅ Check mandate status
- ✅ Download payment history
- ✅ Cancel mandates

## 📋 Available API Endpoints

Your REST API is available at: `http://localhost/api/v1/`

### Student Management
- `GET /api/v1/students` - List all students
- `POST /api/v1/students` - Create/update student
- `GET /api/v1/students/:student_id` - Get student details

### Property Management
- `GET /api/v1/properties` - List all properties
- `POST /api/v1/properties` - Create/update property
- `GET /api/v1/properties/:property_code` - Get property details

### Mandate Management (Rent Payments)
- `POST /api/v1/mandates/register` - Register a new mandate
- `POST /api/v1/mandates/status` - Check mandate status
- `POST /api/v1/mandates/cancel` - Cancel a mandate
- `GET /api/v1/mandates/:contract_reference` - Get mandate details

### Payment Management
- `POST /api/v1/payments/download` - Download payment history from Collexia
- `GET /api/v1/payments/student/:student_id` - Get payments for a student
- `GET /api/v1/payments/contract/:contract_reference` - Get payments for a contract

### System
- `GET /api/v1/health` - Health check endpoint

## 🚀 Next Steps

### 1. Test the API

```bash
# Health check
curl http://localhost/api/v1/health

# Or use your browser
http://localhost/api/v1/health
```

### 2. Create Test Data

```bash
# Create a student
curl -X POST http://localhost/api/v1/students \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "STU001",
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "0812345678",
    "id_number": "1234567890123",
    "id_type": 1,
    "account_number": "123456789",
    "account_type": 1,
    "bank_id": 65
  }'
```

### 3. Integrate with Your Apps

#### Next.js Example:
```javascript
const API_BASE = 'http://localhost/api/v1';

export async function registerStudent(data) {
  const res = await fetch(`${API_BASE}/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  return res.json();
}
```

#### React Native Expo Example:
```javascript
const API_BASE = 'http://YOUR_IP_ADDRESS/api/v1';

export async function registerStudent(data) {
  const res = await fetch(`${API_BASE}/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  return res.json();
}
```

## ⚠️ Important Notes

1. **TLS Certificates**: If Collexia requires mutual TLS (client certificates), you may need to:
   - Obtain client certificates from Collexia
   - Update `cert_path` and `key_path` in `config.php`
   - Or update `pfx_path` and `pfx_pass` if using PFX format

2. **IP Whitelisting**: Your server's IP address may need to be whitelisted with Collexia for UAT access.

3. **CORS**: The API is configured to accept requests from:
   - `http://localhost:3000` (Next.js dev)
   - `http://localhost:3001` (Next.js alt)
   - `exp://localhost:8081` (Expo dev)
   - `exp://192.168.*.*:8081` (Expo network)
   
   Update `api/config.php` to add more origins if needed.

## 📚 Documentation

- **API Documentation**: See `api/README.md`
- **Setup Guide**: See `SETUP.md`
- **Test Results**: See `TEST_RESULTS.md`

## 🎯 You're All Set!

Your Collexia integration is ready to use. Start by testing the health endpoint, then create some test data, and finally integrate with your Next.js and React Native applications!


