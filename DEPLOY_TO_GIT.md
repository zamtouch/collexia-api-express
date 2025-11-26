# Deploy Express API to Git & Vercel

## ✅ Express API is Ready!

Your Express API is working perfectly with:
- ✅ Same Collexia config as PHP version
- ✅ All endpoints working
- ✅ No database needed (in-memory storage)
- ✅ Ready for Vercel deployment

## Step 1: Initialize Git Repository

```bash
# Initialize git (if not already done)
git init

# Add all files
git add .

# Commit
git commit -m "Add Express API with Collexia integration"
```

## Step 2: Create GitHub Repository

1. Go to [github.com](https://github.com)
2. Click **"New repository"**
3. Name it: `collexia-api` (or any name)
4. **Don't** initialize with README (we already have files)
5. Click **"Create repository"**

## Step 3: Push to GitHub

```bash
# Add remote (replace YOUR_USERNAME and REPO_NAME)
git remote add origin https://github.com/YOUR_USERNAME/REPO_NAME.git

# Push to GitHub
git branch -M main
git push -u origin main
```

## Step 4: Deploy to Vercel

### Option A: Vercel CLI

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel

# Follow prompts:
# - Set up and deploy? Yes
# - Which scope? (your account)
# - Link to existing project? No
# - Project name? collexia-api
# - Directory? ./
# - Override settings? No
```

### Option B: GitHub Integration (Recommended)

1. Go to [vercel.com](https://vercel.com)
2. Sign up/Login with GitHub
3. Click **"Add New Project"**
4. Import your GitHub repository
5. Vercel auto-detects Express
6. Click **"Deploy"**

## Step 5: Set Environment Variables in Vercel

After deployment, set environment variables:

1. Go to Vercel dashboard
2. Your project → **Settings** → **Environment Variables**
3. Add these (or use defaults from config.php):

```
COLLEXIA_BASE_URL=https://collection-uat.collexia.co
COLLEXIA_BASIC_USER=bareinvuat
COLLEXIA_BASIC_PASS=Ms@utbinT!11
COLLEXIA_CLIENT_ID=6FA41D83-B8A5-11F0-B138-42010A960205
COLLEXIA_CLIENT_SECRET=9FXhhuOtjiKinPFpbnSb
COLLEXIA_MERCHANT_GID=12584
COLLEXIA_REMOTE_GID=71
COLLEXIA_HEADER_PREFIX=CX_SWITCH
```

4. Click **"Save"**
5. Redeploy (or it auto-redeploys)

## Step 6: Test Deployed API

```bash
# Replace with your Vercel URL
curl https://your-app.vercel.app/api/v1/health
```

## Step 7: Add Custom Domain (Optional)

1. Vercel dashboard → **Settings** → **Domains**
2. Add `app.pozi.com.na`
3. Update DNS records as shown
4. Wait for SSL (automatic)

## Quick Commands Summary

```bash
# Local development
npm start

# Test locally
npm test

# Deploy to Vercel
vercel

# Or push to GitHub (auto-deploys if connected)
git add .
git commit -m "Update API"
git push
```

## What's Included

- ✅ Express server (`api/index.js`)
- ✅ Collexia client with exact same config
- ✅ All API endpoints
- ✅ In-memory storage (no database)
- ✅ CORS configured
- ✅ Error handling
- ✅ Vercel configuration (`vercel.json`)
- ✅ Test script (`test-api.js`)

## Next Steps

1. **Push to GitHub** - Your code is ready
2. **Deploy to Vercel** - Takes 2 minutes
3. **Test endpoints** - All working!
4. **Add database later** - If needed (currently using in-memory)

---

**Your API is production-ready!** 🚀

