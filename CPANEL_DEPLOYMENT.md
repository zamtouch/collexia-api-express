# cPanel Deployment Guide for Collexia API

This guide will help you deploy the Collexia Rental Payment API to your cPanel hosting at `https://app.pozi.com.na`.

## Pre-Deployment Checklist

- [ ] PHP 7.4 or higher (PHP 8.1+ recommended)
- [ ] MySQL/MariaDB database access
- [ ] cPanel access with file manager or FTP
- [ ] Collexia API credentials (UAT or Production)
- [ ] TLS certificates (if required by Collexia)

## Step 1: Upload Files to cPanel

### Option A: Using cPanel File Manager
1. Log into your cPanel
2. Navigate to **File Manager**
3. Go to your domain's root directory (usually `public_html` or `app.pozi.com.na`)
4. Upload all project files maintaining the directory structure:
   ```
   /public_html/
   ├── api/
   ├── src/
   ├── docs/
   ├── logs/
   ├── config.php
   ├── .htaccess
   └── README.md
   ```

### Option B: Using FTP/SFTP
1. Connect to your server via FTP/SFTP
2. Upload all files maintaining directory structure
3. Ensure file permissions are correct (see Step 3)

## Step 2: Database Setup

1. **Create Database in cPanel:**
   - Go to **MySQL Databases** in cPanel
   - Create a new database: `yourusername_collexia_rentals`
   - Note the full database name (usually prefixed with your cPanel username)

2. **Create Database User:**
   - Create a new MySQL user
   - Assign a strong password
   - Add the user to the database with **ALL PRIVILEGES**

3. **Set Environment Variables:**
   
   In cPanel, you can set environment variables in two ways:
   
   **Method 1: Using .htaccess (Recommended)**
   Add these lines to your `.htaccess` file (before the RewriteEngine line):
   ```apache
   SetEnv DB_HOST localhost
   SetEnv DB_NAME yourusername_collexia_rentals
   SetEnv DB_USER yourusername_dbuser
   SetEnv DB_PASS your_secure_password
   SetEnv APP_ENV production
   ```
   
   **Method 2: Using cPanel Environment Variables**
   - Go to **Environment Variables** in cPanel
   - Add each variable:
     - `DB_HOST` = `localhost`
     - `DB_NAME` = `yourusername_collexia_rentals`
     - `DB_USER` = `yourusername_dbuser`
     - `DB_PASS` = `your_secure_password`
     - `APP_ENV` = `production`

4. **Update Database Configuration:**
   
   The `api/database.php` file will automatically use these environment variables. No code changes needed!

## Step 3: File Permissions

Set correct file permissions via cPanel File Manager or SSH:

```bash
# Directories should be 755
chmod 755 api/
chmod 755 src/
chmod 755 logs/
chmod 755 docs/

# Files should be 644
chmod 644 api/*.php
chmod 644 src/*.php
chmod 644 config.php
chmod 644 .htaccess

# Logs directory must be writable (755 or 775)
chmod 755 logs/
# Or if you need group write access:
chmod 775 logs/
```

## Step 4: Collexia Configuration

1. **Update `config.php` with Production Credentials:**
   
   Edit `config.php` in the root directory:
   ```php
   return [
       'base_url'      => 'https://collection.collexia.co', // Production URL
       'base_path'     => '/api/coswitchuadsrest/v3',
       'basic_user'    => 'your_production_username',
       'basic_pass'    => 'your_production_password',
       'client_id'     => 'your_production_client_id',
       'client_secret' => 'your_production_client_secret',
       'merchant_gid'  => your_production_merchant_gid,
       'remote_gid'    => your_production_remote_gid,
       // ... TLS certificates if required
   ];
   ```

2. **TLS Certificates (if required):**
   - Upload your client certificate and key to a secure location (e.g., `private/certs/`)
   - Update paths in `config.php`:
     ```php
     'cert_path' => __DIR__ . '/private/certs/client.crt',
     'key_path'  => __DIR__ . '/private/certs/client.key',
     ```
   - Set permissions: `chmod 600` for certificate files

## Step 5: CORS Configuration

The CORS configuration in `api/config.php` already includes your production domain:
- ✅ `https://app.pozi.com.na`
- ✅ `https://www.pozi.com.na`

If you need to add more domains, edit `api/config.php` and add them to the `ALLOWED_ORIGINS` array.

## Step 6: Test the Deployment

1. **Test Health Endpoint:**
   ```bash
   curl https://app.pozi.com.na/api/v1/health
   ```
   
   Expected response:
   ```json
   {
     "success": true,
     "message": "API is running",
     "timestamp": "2025-01-15 10:30:00"
   }
   ```

2. **Test Database Connection:**
   The database tables will be created automatically on the first API request. Check the logs:
   ```bash
   tail -f logs/error.log
   ```

3. **Test API Endpoints:**
   Use Postman or curl to test:
   - `GET /api/v1/students` - Should return empty array initially
   - `POST /api/v1/students` - Create a test student
   - `GET /api/v1/properties` - List properties

## Step 7: Security Hardening

1. **Protect Sensitive Files:**
   Add to `.htaccess`:
   ```apache
   # Protect config files
   <FilesMatch "^(config\.php|\.env)$">
       Order allow,deny
       Deny from all
   </FilesMatch>
   
   # Protect logs directory
   <DirectoryMatch "^.*/logs/">
       Order allow,deny
       Deny from all
   </DirectoryMatch>
   ```

2. **Disable Directory Listing:**
   Add to `.htaccess`:
   ```apache
   Options -Indexes
   ```

3. **Set Production Error Reporting:**
   The code automatically detects production mode via `APP_ENV=production` environment variable.

## Step 8: SSL/HTTPS Configuration

Ensure your domain has SSL enabled:
1. Go to **SSL/TLS** in cPanel
2. Install SSL certificate (Let's Encrypt is free)
3. Force HTTPS redirect by adding to `.htaccess`:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

## Troubleshooting

### Issue: 404 Errors on API Endpoints

**Solution:**
- Check that `.htaccess` is uploaded correctly
- Verify `mod_rewrite` is enabled (contact hosting support)
- Check file permissions on `.htaccess` (should be 644)

### Issue: Database Connection Failed

**Solution:**
- Verify environment variables are set correctly
- Check database credentials in cPanel MySQL Databases
- Ensure database user has proper permissions
- Check `logs/error.log` for detailed error messages

### Issue: CORS Errors

**Solution:**
- Verify your frontend domain is in `ALLOWED_ORIGINS` in `api/config.php`
- Check that CORS headers are being sent (use browser DevTools Network tab)

### Issue: Permission Denied Errors

**Solution:**
- Ensure `logs/` directory is writable (755 or 775)
- Check file ownership matches web server user
- Contact hosting support if issues persist

### Issue: Collexia API Errors

**Solution:**
- Verify credentials in `config.php` are correct
- Check TLS certificate paths if using mutual TLS
- Review `logs/error.log` for detailed error messages
- Ensure your server IP is whitelisted with Collexia (if required)

## Post-Deployment

1. **Monitor Logs:**
   Regularly check `logs/error.log` for issues

2. **Backup Database:**
   Set up regular database backups in cPanel

3. **Update Documentation:**
   Update your frontend applications to use:
   - Base URL: `https://app.pozi.com.na`
   - API Base: `https://app.pozi.com.na/api/v1`

4. **Test All Endpoints:**
   Run through all API endpoints to ensure everything works

## Support

If you encounter issues:
1. Check `logs/error.log` for detailed error messages
2. Verify all environment variables are set correctly
3. Test database connection independently
4. Contact hosting support for server-level issues

---

**Deployment Status:** ✅ Ready for cPanel deployment
**Last Updated:** 2025-01-15

