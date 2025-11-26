# Deploying to Railway (Recommended)

Railway is the **easiest and best option** for hosting your PHP API. It's designed for exactly this use case.

## Why Railway?

✅ **Native PHP Support** - No workarounds needed  
✅ **Easy Database Setup** - One-click MySQL database  
✅ **Persistent File System** - Logs work normally  
✅ **No Cold Starts** - Always ready  
✅ **Free Tier** - $5 credit monthly  
✅ **Simple Deployment** - Connect GitHub and deploy  
✅ **Environment Variables** - Easy configuration  

## Step-by-Step Deployment

### 1. Sign Up

Go to [railway.app](https://railway.app) and sign up (GitHub login works).

### 2. Create New Project

1. Click **"New Project"**
2. Select **"Deploy from GitHub repo"**
3. Connect your repository
4. Select the `collexia` repository

### 3. Add MySQL Database

1. In your project, click **"+ New"**
2. Select **"Database"**
3. Choose **"MySQL"**
4. Railway will automatically create the database

### 4. Set Environment Variables

In your project settings, add these environment variables:

```env
DB_HOST=${{MySQL.MYSQLHOST}}
DB_NAME=${{MySQL.MYSQLDATABASE}}
DB_USER=${{MySQL.MYSQLUSER}}
DB_PASS=${{MySQL.MYSQLPASSWORD}}
DB_PORT=${{MySQL.MYSQLPORT}}

APP_ENV=production

COLLEXIA_BASE_URL=https://collection.collexia.co
COLLEXIA_BASIC_USER=your_username
COLLEXIA_BASIC_PASS=your_password
COLLEXIA_CLIENT_ID=your_client_id
COLLEXIA_CLIENT_SECRET=your_client_secret
COLLEXIA_MERCHANT_GID=12584
COLLEXIA_REMOTE_GID=71
```

**Note:** Railway automatically provides database connection variables. Use `${{MySQL.MYSQLHOST}}` etc.

### 5. Configure Build Settings

Railway will auto-detect PHP, but you can set:

**Start Command:**
```bash
php -S 0.0.0.0:$PORT -t .
```

Or use the `railway.json` file I created.

### 6. Deploy!

Railway will automatically:
- Build your project
- Install dependencies (if any)
- Start your PHP server
- Give you a URL like `https://your-app.railway.app`

### 7. Update Routing

Since Railway doesn't use `.htaccess`, you need to create a simple router file.

Create `index.php` in the root:

```php
<?php
// Root index.php for Railway
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route API requests
if (strpos($path, '/api/') === 0) {
    // Extract the path after /api/
    $apiPath = substr($path, 5); // Remove '/api/'
    $_GET['path'] = $apiPath;
    require __DIR__ . '/api/index.php';
} else {
    // Health check or redirect
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Collexia API',
        'endpoints' => '/api/v1/*'
    ]);
}
```

### 8. Test Your API

```bash
curl https://your-app.railway.app/api/v1/health
```

## Custom Domain

1. In Railway project settings
2. Click **"Settings"** → **"Domains"**
3. Add your domain: `app.pozi.com.na`
4. Railway will give you DNS records to add

## Database Migration

The database tables will auto-create on first API request, but you can also run migrations manually if needed.

## Monitoring

Railway provides:
- Real-time logs
- Metrics dashboard
- Error tracking
- Automatic restarts

## Cost

- **Free Tier:** $5 credit/month (usually enough for small apps)
- **Pro:** $20/month for more resources

## Troubleshooting

### Issue: 404 on API endpoints

**Fix:** Make sure you have the root `index.php` file routing to `api/index.php`

### Issue: Database connection failed

**Fix:** Check that environment variables use Railway's template syntax: `${{MySQL.MYSQLHOST}}`

### Issue: Port binding error

**Fix:** Make sure start command uses `$PORT` variable: `php -S 0.0.0.0:$PORT`

---

## Why Railway > Vercel for PHP

| Feature | Railway | Vercel |
|---------|---------|--------|
| PHP Support | ✅ Native | ⚠️ Serverless only |
| Database | ✅ One-click | ❌ External only |
| File System | ✅ Persistent | ❌ Read-only |
| Cold Starts | ✅ None | ❌ Yes |
| Setup Time | ✅ 5 minutes | ⚠️ 30+ minutes |
| Cost | ✅ $5/month | ✅ Free tier |

---

**Ready to deploy?** Just connect your GitHub repo and Railway handles the rest!

