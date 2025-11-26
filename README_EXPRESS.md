# Collexia Express API

Express/Node.js version of the Collexia Rental Payment API - **No database required!** Uses in-memory storage for testing.

## Quick Start

### 1. Install Dependencies

```bash
npm install
```

### 2. Run the Server

```bash
npm start
```

Server runs on `http://localhost:3000`

### 3. Test the API

```bash
npm test
```

Or manually:

```bash
# Health check
curl http://localhost:3000/api/v1/health

# Create student
curl -X POST http://localhost:3000/api/v1/students \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "STU001",
    "full_name": "John Doe",
    "email": "john@example.com",
    "account_number": "123456789",
    "bank_id": 65
  }'
```

## Configuration

Uses the **exact same config** as the PHP version:

- `COLLEXIA_BASE_URL` - Default: `https://collection-uat.collexia.co`
- `COLLEXIA_BASIC_USER` - Default: `bareinvuat`
- `COLLEXIA_BASIC_PASS` - Default: `Ms@utbinT!11`
- `COLLEXIA_CLIENT_ID` - Default: `6FA41D83-B8A5-11F0-B138-42010A960205`
- `COLLEXIA_CLIENT_SECRET` - Default: `9FXhhuOtjiKinPFpbnSb`
- `COLLEXIA_MERCHANT_GID` - Default: `12584`
- `COLLEXIA_REMOTE_GID` - Default: `71`
- `COLLEXIA_HEADER_PREFIX` - Default: `CX_SWITCH`

Set environment variables to override defaults.

## API Endpoints

All endpoints work the same as PHP version:

- `GET /api/v1/health` - Health check
- `GET /api/v1/students` - List students
- `POST /api/v1/students` - Create student
- `GET /api/v1/students/:student_id` - Get student
- `GET /api/v1/properties` - List properties
- `POST /api/v1/properties` - Create property
- `GET /api/v1/properties/:property_code` - Get property
- `POST /api/v1/mandates/register` - Register mandate
- `POST /api/v1/mandates/status` - Check mandate status
- `GET /api/v1/mandates/:contract_reference` - Get mandate
- `POST /api/v1/payments/download` - Download payments
- `GET /api/v1/payments/student/:student_id` - Student payments
- `GET /api/v1/payments/contract/:contract_reference` - Contract payments

## Deploy to Vercel

```bash
npm i -g vercel
vercel
```

Or connect GitHub repo to Vercel - it auto-detects Express!

## Notes

- **In-memory storage** - Data resets on server restart (perfect for testing)
- **Same Collexia config** - Uses exact same credentials as PHP version
- **No database needed** - Works out of the box
- **Ready for Vercel** - Deploys instantly

