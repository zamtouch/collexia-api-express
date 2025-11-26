# Collexia Rental Payment API

Express/Node.js REST API for managing student rental payments through Collexia payment collection system.

## 🚀 Quick Start

```bash
# Install dependencies
npm install

# Start server
npm start

# Test API
npm test
```

Server runs on `http://localhost:3000`

## 📁 Project Structure

```
.
├── api/
│   ├── index.js              # Main Express application
│   ├── routes/               # API route handlers
│   │   ├── students.js
│   │   ├── properties.js
│   │   ├── mandates.js
│   │   └── payments.js
│   ├── utils/                # Utilities
│   │   ├── CollexiaClient.js
│   │   ├── contractReference.js
│   │   └── validator.js
│   └── config/
│       └── database.js       # Database config (for future use)
├── src/
│   └── CollexiaClient.php    # PHP reference implementation
├── docs/                     # Collexia API specifications
├── package.json
├── vercel.json               # Vercel deployment config
├── test-endpoints.js         # Comprehensive test suite
└── test-api.js              # Simple test script
```

## 📡 API Endpoints

### Health
- `GET /api/v1/health` - Health check

### Students
- `GET /api/v1/students` - List all students
- `POST /api/v1/students` - Create/update student
- `GET /api/v1/students/:student_id` - Get student details

### Properties
- `GET /api/v1/properties` - List all properties
- `POST /api/v1/properties` - Create/update property
- `GET /api/v1/properties/:property_code` - Get property details

### Mandates
- `POST /api/v1/mandates/register` - Register a new mandate
- `POST /api/v1/mandates/status` - Check mandate status
- `GET /api/v1/mandates/:contract_reference` - Get mandate details

### Payments
- `POST /api/v1/payments/download` - Download payment history
- `GET /api/v1/payments/student/:student_id` - Get student payments
- `GET /api/v1/payments/contract/:contract_reference` - Get contract payments

## ⚙️ Configuration

Uses environment variables (with defaults matching PHP version):

```env
COLLEXIA_BASE_URL=https://collection-uat.collexia.co
COLLEXIA_BASIC_USER=bareinvuat
COLLEXIA_BASIC_PASS=Ms@utbinT!11
COLLEXIA_CLIENT_ID=6FA41D83-B8A5-11F0-B138-42010A960205
COLLEXIA_CLIENT_SECRET=9FXhhuOtjiKinPFpbnSb
COLLEXIA_MERCHANT_GID=12584
COLLEXIA_REMOTE_GID=71
COLLEXIA_HEADER_PREFIX=CX_SWITCH
```

## 🧪 Testing

```bash
# Run full test suite
node test-endpoints.js

# Run simple tests
npm test
```

## 📦 Deployment

### Deploy to Vercel

```bash
npm i -g vercel
vercel
```

Or connect GitHub repo to Vercel for automatic deployments.

## 📚 Documentation

- `HOW_TO_TEST.md` - Complete testing guide
- `NEXT_STEPS.md` - Deployment instructions
- `EXPRESS_VERCEL_DEPLOYMENT.md` - Detailed Vercel guide

## 🏗️ Project Structure

```
.
├── api/
│   ├── index.js              # Main Express app
│   ├── utils/
│   │   ├── CollexiaClient.js # Collexia API client
│   │   └── contractReference.js
│   └── config/
│       └── database.js       # Database config (not used currently)
├── package.json
├── vercel.json               # Vercel configuration
└── test-endpoints.js         # Test suite
```

## ✨ Features

- ✅ Express/Node.js API
- ✅ Same Collexia config as PHP version
- ✅ In-memory storage (no database needed)
- ✅ CORS configured
- ✅ Error handling
- ✅ Ready for Vercel deployment

## 📝 License

MIT
