# Collexia Rental Payment API - Setup Guide

## Quick Start

This PHP API provides a simple interface for managing student rental payments through the Collexia payment collection system. It works with Next.js and React Native Expo applications.

## Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Apache with mod_rewrite (or Nginx)
- Composer (optional, for future dependencies)

## Installation Steps

### 1. Database Setup

Create a MySQL database:

```sql
CREATE DATABASE collexia_rentals CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update database credentials in `api/database.php`:

```php
private $host = 'localhost';
private $dbname = 'collexia_rentals';
private $username = 'root';
private $password = 'your_password';
```

The database tables will be created automatically on the first API request.

### 2. Collexia Configuration

Update `config.php` with your Collexia credentials:

```php
return [
    'base_url' => 'https://collection-uat.collexia.co', // UAT environment
    // 'base_url' => 'https://collection.collexia.co', // Production
    'base_path' => '/api/coswitchuadsrest/v3',
    'basic_user' => 'your_username',
    'basic_pass' => 'your_password',
    'header_prefix' => 'X-COLLEXIA',
    'client_id' => 'your_client_id',
    'client_secret' => 'your_client_secret',
    'merchant_gid' => 12345, // Your merchant GID
    'remote_gid' => 27,      // Your remote GID
    'cert_path' => '',       // Path to client certificate (if required)
    'key_path' => '',        // Path to client key (if required)
    'pfx_path' => '',        // Path to PFX certificate (if required)
    'pfx_pass' => '',        // PFX passphrase (if required)
];
```

### 3. Web Server Configuration

#### Apache (XAMPP)

The `.htaccess` file is already configured. Make sure `mod_rewrite` is enabled:

1. Open `httpd.conf`
2. Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Restart Apache

#### Nginx

Add this to your server block:

```nginx
location /api/ {
    try_files $uri $uri/ /api/index.php?$query_string;
}
```

### 4. File Permissions

Ensure the `logs` directory is writable:

```bash
chmod 755 logs
```

### 5. Test the API

Test the health endpoint:

```bash
curl http://localhost/api/v1/health
```

You should receive:

```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2025-01-15 10:30:00"
}
```

## API Usage Examples

### 1. Register a Student

```bash
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

### 2. Register a Property

```bash
curl -X POST http://localhost/api/v1/properties \
  -H "Content-Type: application/json" \
  -d '{
    "property_code": "PROP001",
    "property_name": "Student Residence Block A",
    "address": "123 Main Street, Windhoek",
    "monthly_rent": 5000.00
  }'
```

### 3. Setup Rent Payment (Register Mandate)

```bash
curl -X POST http://localhost/api/v1/mandates/register \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "STU001",
    "property_id": 1,
    "monthly_rent": 5000.00,
    "start_date": "20250101",
    "frequency_code": 4,
    "no_of_installments": 12,
    "tracking_days": 3,
    "mag_id": 46
  }'
```

### 4. Check Mandate Status

```bash
curl -X POST http://localhost/api/v1/mandates/status \
  -H "Content-Type: application/json" \
  -d '{
    "contract_reference": "2A903943000020"
  }'
```

### 5. Download Payment History

```bash
curl -X POST http://localhost/api/v1/payments/download \
  -H "Content-Type: application/json"
```

## Integration with Next.js

```javascript
// lib/api.js
const API_BASE_URL = 'http://localhost/api/v1';

export async function registerStudent(studentData) {
  const response = await fetch(`${API_BASE_URL}/students`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(studentData),
  });
  return response.json();
}

export async function registerMandate(mandateData) {
  const response = await fetch(`${API_BASE_URL}/mandates/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(mandateData),
  });
  return response.json();
}
```

## Integration with React Native Expo

```javascript
// services/api.js
const API_BASE_URL = 'http://your-server-ip/api/v1';

export async function registerStudent(studentData) {
  const response = await fetch(`${API_BASE_URL}/students`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(studentData),
  });
  return response.json();
}
```

**Note:** For Expo, you'll need to use your computer's IP address instead of `localhost`. Update CORS settings in `api/config.php` to allow your IP.

## Troubleshooting

### Database Connection Error

- Check database credentials in `api/database.php`
- Ensure MySQL is running
- Verify database exists

### API Returns 404

- Check `.htaccess` is working (Apache)
- Verify `mod_rewrite` is enabled
- Check file paths are correct

### CORS Errors

- Update `ALLOWED_ORIGINS` in `api/config.php`
- Ensure your frontend URL is in the allowed list

### Collexia API Errors

- Verify credentials in `config.php`
- Check Collexia API endpoint is accessible
- Review error logs in `logs/error.log`

## Next Steps

1. Set up your Collexia credentials
2. Test with sample data
3. Integrate with your Next.js/React Native apps
4. Set up a cron job to periodically download payment history
5. Add authentication/authorization as needed

## Support

For Collexia API documentation, refer to the files in the `docs/` directory.



