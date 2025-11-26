# Collexia Rental Payment API

A PHP REST API for managing student rental payments through Collexia payment collection system.

## Setup

### 1. Database Configuration

Update the database credentials in `api/database.php`:

```php
private $host = 'localhost';
private $dbname = 'collexia_rentals';
private $username = 'root';
private $password = '';
```

### 2. Create Database

```sql
CREATE DATABASE collexia_rentals CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The tables will be created automatically on first API request.

### 3. Collexia Configuration

Update `config.php` with your Collexia credentials:

```php
return [
    'base_url' => 'https://collection-uat.collexia.co', // or production URL
    'merchant_gid' => 'YOUR_MERCHANT_GID',
    'remote_gid' => 'YOUR_REMOTE_GID',
    'client_id' => 'YOUR_CLIENT_ID',
    'client_secret' => 'YOUR_CLIENT_SECRET',
    'basic_user' => 'YOUR_USERNAME',
    'basic_pass' => 'YOUR_PASSWORD',
    // ... TLS certificates if required
];
```

### 4. Web Server Configuration

#### Apache (.htaccess included)
The `.htaccess` file is already configured to route `/api/*` requests to `api/index.php`.

#### Nginx
```nginx
location /api/ {
    try_files $uri $uri/ /api/index.php?$query_string;
}
```

## API Endpoints

### Students

- `GET /api/v1/students` - List all students
- `POST /api/v1/students` - Create/update student
- `GET /api/v1/students/:student_id` - Get student details

### Properties

- `GET /api/v1/properties` - List all properties
- `POST /api/v1/properties` - Create/update property
- `GET /api/v1/properties/:property_code` - Get property details

### Mandates (Rent Payment Setup)

- `POST /api/v1/mandates/register` - Register a new mandate for rent collection
- `POST /api/v1/mandates/status` - Check mandate status
- `POST /api/v1/mandates/cancel` - Cancel a mandate
- `GET /api/v1/mandates/:contract_reference` - Get mandate details

### Payments

- `POST /api/v1/payments/download` - Download payment history from Collexia
- `GET /api/v1/payments/student/:student_id` - Get payment history for a student
- `GET /api/v1/payments/contract/:contract_reference` - Get payment history for a contract

## Example Requests

### Register a Student

```bash
POST /api/v1/students
Content-Type: application/json

{
  "student_id": "STU001",
  "full_name": "John Doe",
  "email": "john@example.com",
  "phone": "0812345678",
  "id_number": "1234567890123",
  "id_type": 1,
  "account_number": "123456789",
  "account_type": 1,
  "bank_id": 65
}
```

### Register a Property

```bash
POST /api/v1/properties
Content-Type: application/json

{
  "property_code": "PROP001",
  "property_name": "Student Residence Block A",
  "address": "123 Main Street, Windhoek",
  "monthly_rent": 5000.00
}
```

### Register a Mandate (Setup Rent Payment)

```bash
POST /api/v1/mandates/register
Content-Type: application/json

{
  "student_id": "STU001",
  "property_id": 1,
  "monthly_rent": 5000.00,
  "start_date": "20250101",
  "frequency_code": 4,
  "no_of_installments": 12,
  "tracking_days": 3,
  "mag_id": 46
}
```

### Check Mandate Status

```bash
POST /api/v1/mandates/status
Content-Type: application/json

{
  "contract_reference": "2A903943000020"
}
```

## Response Format

All responses follow this format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... },
  "timestamp": "2025-01-15 10:30:00"
}
```

Error responses:

```json
{
  "success": false,
  "message": "Error message",
  "errors": [ ... ],
  "timestamp": "2025-01-15 10:30:00"
}
```

## CORS Configuration

The API is configured to accept requests from:
- `http://localhost:3000` (Next.js dev)
- `http://localhost:3001` (Next.js alt)
- `exp://localhost:8081` (Expo dev)
- `exp://192.168.*.*:8081` (Expo network)

Update `api/config.php` to add more origins.

## Bank IDs

- 64 - Bank Windhoek
- 65 - FNB Namibia
- 66 - TrustCo Bank
- 67 - Bank Atlántico
- 68 - BankBIC
- 69 - Bank of Namibia
- 70 - Letshego Bank Namibia
- 71 - Nedbank Namibia
- 72 - Standard Bank Namibia

## Account Types

- 1 - Current (Cheque)
- 2 - Savings
- 3 - Transmission

## Frequency Codes

- 1 - Weekly
- 3 - Fortnightly
- 4 - Monthly
- 5 - Monthly by Rule

## Notes

- The API automatically generates contract references using the format specified by Collexia
- Payment history should be downloaded periodically to sync with Collexia
- Mandates must be checked for status after registration to confirm they were loaded successfully



