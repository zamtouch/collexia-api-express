# Express/Node.js Version - Vercel Deployment

## ✅ Yes! Express works perfectly on Vercel!

I've converted your PHP API to Express (Node.js). Vercel has **excellent** support for Express and Node.js.

## What I Created

### Express API Structure:
- ✅ `package.json` - Dependencies and scripts
- ✅ `api/index.js` - Main Express app
- ✅ `api/config/database.js` - MySQL connection (mysql2)
- ✅ `api/routes/` - All your routes:
  - `students.js` - Student management
  - `properties.js` - Property management
  - `mandates.js` - Mandate registration
  - `payments.js` - Payment history
- ✅ `api/utils/CollexiaClient.js` - Collexia API client (Node.js)
- ✅ `api/utils/validator.js` - Validation utilities
- ✅ `api/utils/contractReference.js` - Contract reference generator
- ✅ `vercel.json` - Vercel configuration

## Quick Deployment to Vercel

### 1. Install Dependencies

```bash
npm install
```

### 2. Set Environment Variables

Create a `.env` file (or set in Vercel dashboard):

```env
# Database
DB_HOST=your-database-host
DB_NAME=collexia_rentals
DB_USER=your-database-user
DB_PASS=your-database-password

# Collexia
COLLEXIA_BASE_URL=https://collection.collexia.co
COLLEXIA_BASIC_USER=your_username
COLLEXIA_BASIC_PASS=your_password
COLLEXIA_CLIENT_ID=your_client_id
COLLEXIA_CLIENT_SECRET=your_client_secret
COLLEXIA_MERCHANT_GID=12584
COLLEXIA_REMOTE_GID=71
```

### 3. Deploy to Vercel

**Option A: Vercel CLI**
```bash
npm i -g vercel
vercel
```

**Option B: GitHub Integration**
1. Push code to GitHub
2. Go to [vercel.com](https://vercel.com)
3. Import your repository
4. Vercel auto-detects Express
5. Add environment variables
6. Deploy!

### 4. Set Environment Variables in Vercel

In Vercel dashboard:
1. Go to your project
2. Settings → Environment Variables
3. Add all variables from `.env`

## Database Setup

You'll need an external MySQL database. Options:

### Option 1: PlanetScale (Recommended)
- Free tier available
- Serverless MySQL
- Perfect for Vercel

### Option 2: Railway
- Add MySQL database
- Get connection string
- Use in environment variables

### Option 3: Any MySQL Host
- Traditional hosting
- cPanel MySQL
- Just need connection details

## API Endpoints (Same as PHP version)

All endpoints work exactly the same:

- `GET /api/v1/health` - Health check
- `GET /api/v1/students` - List students
- `POST /api/v1/students` - Create student
- `GET /api/v1/students/:student_id` - Get student
- `GET /api/v1/properties` - List properties
- `POST /api/v1/properties` - Create property
- `GET /api/v1/properties/:property_code` - Get property
- `POST /api/v1/mandates/register` - Register mandate
- `POST /api/v1/mandates/status` - Check status
- `GET /api/v1/mandates/:contract_reference` - Get mandate
- `POST /api/v1/payments/download` - Download payments
- `GET /api/v1/payments/student/:student_id` - Student payments
- `GET /api/v1/payments/contract/:contract_reference` - Contract payments

## Local Development

```bash
# Install dependencies
npm install

# Run locally
npm run dev

# Server runs on http://localhost:3000
```

## Why Express on Vercel is Better

✅ **Native Support** - Vercel built for Node.js  
✅ **No Cold Starts** - Express runs smoothly  
✅ **Easy Deployment** - Just push to GitHub  
✅ **Free Tier** - Generous limits  
✅ **Fast** - Edge network  
✅ **Auto-scaling** - Handles traffic spikes  
✅ **HTTPS** - Automatic SSL  

## Testing

```bash
# Health check
curl https://your-app.vercel.app/api/v1/health

# Create student
curl -X POST https://your-app.vercel.app/api/v1/students \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "STU001",
    "full_name": "John Doe",
    "email": "john@example.com",
    "account_number": "123456789",
    "bank_id": 65
  }'
```

## Custom Domain

1. In Vercel dashboard
2. Settings → Domains
3. Add `app.pozi.com.na`
4. Update DNS records
5. Done!

## Differences from PHP Version

1. **Database**: Uses `mysql2` instead of PDO
2. **Collexia Client**: Uses `axios` instead of cURL
3. **HMAC**: Uses Node.js `crypto` module
4. **Routing**: Express router instead of custom router
5. **Response**: Express `res.json()` instead of custom Response class

## Migration Notes

- Same API structure
- Same endpoints
- Same request/response format
- Same database schema
- Same business logic

Just different language! 🚀

---

**Ready to deploy?** Just run `vercel` or connect to GitHub!

