# Troubleshooting 500 Internal Server Error

## Quick Fixes

### 1. Check Error Logs

The most important step is to check your error logs:

**In cPanel:**
- Go to **Error Log** in cPanel
- Look for the most recent errors
- The error message will tell you exactly what's wrong

**Or check the logs file:**
```bash
# Via SSH
tail -f logs/error.log

# Or in cPanel File Manager
# Open logs/error.log
```

### 2. Run Diagnostic Script

I've created a diagnostic script to help identify issues:

**Access it via browser:**
```
https://app.pozi.com.na/api/test-deployment.php
```

This will show you:
- PHP version
- Required extensions
- File structure
- Database connection
- Configuration loading
- And more...

### 3. Common Issues & Fixes

#### Issue: .htaccess Syntax Error

**Symptom:** 500 error immediately, no PHP errors in logs

**Fix:** The `.htaccess` file has been updated to support both Apache 2.2 and 2.4. If you still get errors:

1. **Temporarily rename .htaccess:**
   ```bash
   mv .htaccess .htaccess.backup
   ```

2. **Test if API works without .htaccess:**
   ```
   https://app.pozi.com.na/api/index.php?path=v1/health
   ```

3. **If it works, the issue is .htaccess. Try this minimal version:**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_URI} ^(.*/)?api/
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^api/(.*)$ api/index.php?path=$1 [QSA,L]
   ```

#### Issue: Database Connection Failed

**Symptom:** Error in logs about database connection

**Fix:**
1. **Set environment variables in .htaccess** (add before RewriteEngine):
   ```apache
   SetEnv DB_HOST localhost
   SetEnv DB_NAME yourusername_collexia_rentals
   SetEnv DB_USER yourusername_dbuser
   SetEnv DB_PASS your_password
   ```

2. **Or set in cPanel Environment Variables section**

3. **Verify database credentials in cPanel MySQL Databases**

#### Issue: Missing PHP Extensions

**Symptom:** Fatal error about missing class or function

**Fix:**
- Contact your hosting provider to enable:
  - `pdo`
  - `pdo_mysql`
  - `curl`
  - `json`
  - `mbstring`

#### Issue: File Permissions

**Symptom:** Permission denied errors

**Fix:**
```bash
# Set correct permissions
chmod 755 api/
chmod 644 api/*.php
chmod 755 logs/
chmod 644 .htaccess
```

#### Issue: config.php Blocked

**Symptom:** Error about config.php not found or access denied

**Fix:** The `.htaccess` has been updated to allow PHP includes while blocking direct browser access. If still having issues:

1. **Temporarily remove the FilesMatch block** from `.htaccess`
2. **Test if it works**
3. **Re-add security after confirming it works**

#### Issue: Timezone Error

**Symptom:** Error about timezone 'Africa/Windhoek' not found

**Fix:** The code now falls back to UTC if the timezone isn't available. This shouldn't cause a 500 error anymore.

### 4. Step-by-Step Debugging

1. **Enable Error Display Temporarily:**
   
   Edit `api/config.php` and change:
   ```php
   ini_set('display_errors', 1); // Change from 0 to 1
   ```

2. **Check if it's a routing issue:**
   
   Try accessing directly:
   ```
   https://app.pozi.com.na/api/index.php?path=v1/health
   ```
   
   If this works, the issue is with `.htaccess` routing.

3. **Check if it's a PHP syntax error:**
   
   Run this in cPanel Terminal or SSH:
   ```bash
   php -l api/index.php
   php -l api/config.php
   php -l api/database.php
   ```

4. **Check if it's a missing file:**
   
   The diagnostic script (`api/test-deployment.php`) will show you which files are missing.

### 5. Minimal Test

Create a simple test file `test.php` in your root:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";

// Test database
try {
    require_once 'api/database.php';
    $db = Database::getInstance()->getConnection();
    echo "Database: OK<br>";
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
}

// Test config
try {
    $config = require 'config.php';
    echo "Config: OK<br>";
} catch (Exception $e) {
    echo "Config Error: " . $e->getMessage() . "<br>";
}
```

Access: `https://app.pozi.com.na/test.php`

### 6. Contact Information

If you're still getting 500 errors after trying these fixes:

1. **Check cPanel Error Log** - This is the most important
2. **Run the diagnostic script** - `api/test-deployment.php`
3. **Share the error message** from the logs

The error log will tell you exactly what's wrong!

---

## Quick Checklist

- [ ] Checked cPanel Error Log
- [ ] Ran diagnostic script (`api/test-deployment.php`)
- [ ] Verified database credentials
- [ ] Checked file permissions
- [ ] Tested direct access to `api/index.php?path=v1/health`
- [ ] Verified PHP extensions are installed
- [ ] Checked `.htaccess` syntax

---

**Most Common Cause:** Database connection failure or missing environment variables.

