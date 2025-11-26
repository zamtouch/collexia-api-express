# Quick Deployment Checklist for cPanel

Use this checklist when deploying to `https://app.pozi.com.na`

## Pre-Upload

- [ ] All files are ready and tested locally
- [ ] Collexia production credentials are available
- [ ] Database credentials are ready

## Upload & Setup

- [ ] Upload all files to cPanel (maintain directory structure)
- [ ] Set file permissions:
  - [ ] Directories: 755
  - [ ] PHP files: 644
  - [ ] `.htaccess`: 644
  - [ ] `logs/` directory: 755 (writable)
- [ ] Create MySQL database in cPanel
- [ ] Create MySQL user and grant privileges
- [ ] Set environment variables in `.htaccess` or cPanel:
  - [ ] `DB_HOST=localhost`
  - [ ] `DB_NAME=yourusername_collexia_rentals`
  - [ ] `DB_USER=yourusername_dbuser`
  - [ ] `DB_PASS=your_secure_password`
  - [ ] `APP_ENV=production`

## Configuration

- [ ] Update `config.php` with Collexia production credentials
- [ ] Upload TLS certificates (if required) to secure location
- [ ] Update certificate paths in `config.php` (if using TLS)
- [ ] Verify CORS origins in `api/config.php` include your domains

## Testing

- [ ] Test health endpoint: `https://app.pozi.com.na/api/v1/health`
- [ ] Test database connection (tables auto-create on first request)
- [ ] Test student creation: `POST /api/v1/students`
- [ ] Test property creation: `POST /api/v1/properties`
- [ ] Check `logs/error.log` for any errors

## Security

- [ ] SSL certificate installed and HTTPS working
- [ ] Uncomment HTTPS redirect in `.htaccess` (if needed)
- [ ] Verify sensitive files are protected
- [ ] Test that logs directory is not accessible via browser

## Post-Deployment

- [ ] Update frontend applications with new API URL
- [ ] Set up database backups
- [ ] Monitor `logs/error.log` for issues
- [ ] Test all critical API endpoints

## Quick Test Commands

```bash
# Health check
curl https://app.pozi.com.na/api/v1/health

# List students (should return empty array initially)
curl https://app.pozi.com.na/api/v1/students

# Create a test student
curl -X POST https://app.pozi.com.na/api/v1/students \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": "TEST001",
    "full_name": "Test Student",
    "email": "test@example.com",
    "phone": "0812345678",
    "id_number": "1234567890123",
    "id_type": 1,
    "account_number": "123456789",
    "account_type": 1,
    "bank_id": 65
  }'
```

## Common Issues & Quick Fixes

| Issue | Quick Fix |
|-------|-----------|
| 404 on API endpoints | Check `.htaccess` exists and `mod_rewrite` is enabled |
| Database connection failed | Verify environment variables are set correctly |
| Permission denied | Check `logs/` directory is writable (755) |
| CORS errors | Verify domain in `ALLOWED_ORIGINS` in `api/config.php` |

---

**Ready to deploy?** Follow the detailed guide in `CPANEL_DEPLOYMENT.md`

