# Deploying to Vercel

## ⚠️ Important Limitations

Vercel can host PHP, but there are significant limitations:

1. **No File System Persistence** - Logs won't persist between requests
2. **Cold Starts** - First request can be slow
3. **Execution Time Limits** - 10 seconds on free tier, 60 seconds on Pro
4. **Database Connections** - Need external database (not included)
5. **No .htaccess** - Routing handled by Vercel
6. **Read-only File System** - Can't write to logs directory

## Setup Steps

### 1. Install Vercel CLI

```bash
npm i -g vercel
```

### 2. Configure Environment Variables

Create a `.env` file (or set in Vercel dashboard):

```env
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-database-user
DB_PASS=your-database-password
APP_ENV=production

COLLEXIA_BASE_URL=https://collection.collexia.co
COLLEXIA_BASIC_USER=your_username
COLLEXIA_BASIC_PASS=your_password
COLLEXIA_CLIENT_ID=your_client_id
COLLEXIA_CLIENT_SECRET=your_client_secret
COLLEXIA_MERCHANT_GID=12584
COLLEXIA_REMOTE_GID=71
```

### 3. Update Code for Serverless

You'll need to modify `api/config.php` to handle serverless environment:

```php
// Use /tmp for logs in serverless (Vercel)
$logDir = '/tmp';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/error.log');
```

### 4. Deploy

```bash
vercel
```

Or connect your GitHub repo to Vercel dashboard.

## Better Alternatives for PHP

### 🚀 Recommended: Railway

**Why Railway is Better:**
- ✅ Native PHP support
- ✅ Easy database setup
- ✅ Persistent file system
- ✅ No cold starts
- ✅ Free tier available
- ✅ Simple deployment

**Deploy to Railway:**
1. Sign up at [railway.app](https://railway.app)
2. New Project → Deploy from GitHub
3. Add MySQL database
4. Set environment variables
5. Deploy!

### 🎯 Alternative: Render

**Why Render:**
- ✅ Free tier
- ✅ Native PHP support
- ✅ Managed databases
- ✅ Easy setup

**Deploy to Render:**
1. Sign up at [render.com](https://render.com)
2. New Web Service
3. Connect GitHub repo
4. Select PHP environment
5. Add database
6. Deploy!

### 💰 Alternative: DigitalOcean App Platform

**Why DigitalOcean:**
- ✅ Professional hosting
- ✅ Full PHP support
- ✅ Managed databases
- ✅ $5/month starter

## Recommendation

**For your Collexia API, I recommend:**

1. **Railway** (Best for ease of use)
2. **Render** (Best free option)
3. **DigitalOcean** (Best for production)

Vercel is better suited for frontend apps, not PHP APIs with databases.

---

## Quick Fix: Try Railway Instead

Railway is the easiest option. Want me to create a `railway.json` config file?

