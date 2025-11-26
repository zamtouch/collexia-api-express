# Collexia UAT Credentials Setup

## ✅ Configured Credentials

The following credentials have been configured in `config.php`:

- **Endpoint/URL**: `https://collection-uat.collexia.co`
- **Web Application URL**: `/api/coswitchuadsrest/v3`
- **Username**: `bareinvuat`
- **Remote GID**: `71`
- **Origin**: `71`
- **Merchant GID**: `12584` (XXINTEGR)
- **Digital Signature Client ID**: `6FA41D83-B8A5-11F0-B138-42010A960205`

## ⚠️ Required: Add Missing Credentials

You need to add the following credentials manually (received via WhatsApp to +264 81 850 7476):

1. **Password** (`basic_pass`): Add to `config.php` line 15
2. **Client Secret** (`client_secret`): Add to `config.php` line 18

### Quick Setup Steps:

1. Open `config.php`
2. Find line 15 and replace the empty string with your password:
   ```php
   'basic_pass' => 'YOUR_PASSWORD_HERE',
   ```
3. Find line 18 and replace the empty string with your client secret:
   ```php
   'client_secret' => 'YOUR_CLIENT_SECRET_HERE',
   ```

## 🔒 Security Note

For production, consider using environment variables instead of hardcoding credentials:

```bash
# Set environment variables (Windows PowerShell)
$env:COLLEXIA_BASIC_PASS = "your_password"
$env:COLLEXIA_CLIENT_SECRET = "your_client_secret"
```

Or create a `.env` file (if using a library like `vlucas/phpdotenv`).

## ✅ Testing After Setup

Once you've added the password and client secret, you can test the API:

```bash
# Test health endpoint
curl http://localhost/api/v1/health

# Test mandate registration (requires valid credentials)
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

## 📋 Configuration Summary

| Setting | Value | Status |
|---------|-------|--------|
| Base URL | https://collection-uat.collexia.co | ✅ Configured |
| Base Path | /api/coswitchuadsrest/v3 | ✅ Configured |
| Username | bareinvuat | ✅ Configured |
| Password | (from WhatsApp) | ⚠️ Needs to be added |
| Remote GID | 71 | ✅ Configured |
| Origin | 71 | ✅ Configured |
| Merchant GID | 12584 | ✅ Configured |
| Client ID | 6FA41D83-B8A5-11F0-B138-42010A960205 | ✅ Configured |
| Client Secret | (from WhatsApp) | ⚠️ Needs to be added |

## 🚀 Next Steps

1. Add password and client secret to `config.php`
2. Test the health endpoint
3. Create a test student and property
4. Test mandate registration with Collexia UAT
5. Verify payment downloads work



