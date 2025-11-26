# 🚀 Deploy Now - Step by Step

## ✅ Git is Ready!

Your code is committed and ready to push.

## 📋 Next Steps

### Step 1: Create GitHub Repository

1. Go to [github.com](https://github.com) and sign in
2. Click the **"+"** icon → **"New repository"**
3. Repository name: `collexia-api` (or your choice)
4. Description: "Collexia Rental Payment API - Express/Node.js"
5. **Make it Public** (or Private if you prefer)
6. **DO NOT** check "Initialize with README" (we already have files)
7. Click **"Create repository"**

### Step 2: Push to GitHub

After creating the repo, GitHub will show you commands. Use these:

```bash
# Add remote (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/collexia-api.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**Example:**
```bash
git remote add origin https://github.com/johndoe/collexia-api.git
git branch -M main
git push -u origin main
```

### Step 3: Deploy to Vercel

**Option A: GitHub Integration (Easiest)**

1. Go to [vercel.com](https://vercel.com)
2. Sign up/Login with **GitHub**
3. Click **"Add New Project"**
4. Find and select your `collexia-api` repository
5. Vercel will auto-detect Express
6. Click **"Deploy"**
7. Wait ~2 minutes for deployment

**Option B: Vercel CLI**

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel

# Follow prompts:
# - Set up and deploy? → Yes
# - Which scope? → Select your account
# - Link to existing project? → No
# - Project name? → collexia-api
# - Directory? → ./
# - Override settings? → No
```

### Step 4: Set Environment Variables

After deployment:

1. Go to Vercel Dashboard
2. Click your project
3. Go to **Settings** → **Environment Variables**
4. Add these variables:

```
COLLEXIA_BASE_URL = https://collection-uat.collexia.co
COLLEXIA_BASIC_USER = bareinvuat
COLLEXIA_BASIC_PASS = Ms@utbinT!11
COLLEXIA_CLIENT_ID = 6FA41D83-B8A5-11F0-B138-42010A960205
COLLEXIA_CLIENT_SECRET = 9FXhhuOtjiKinPFpbnSb
COLLEXIA_MERCHANT_GID = 12584
COLLEXIA_REMOTE_GID = 71
COLLEXIA_HEADER_PREFIX = CX_SWITCH
```

5. Click **"Save"**
6. Go to **Deployments** tab
7. Click **"Redeploy"** on the latest deployment

### Step 5: Test Deployed API

After deployment, Vercel gives you a URL like:
`https://collexia-api.vercel.app`

Test it:

```bash
curl https://your-app.vercel.app/api/v1/health
```

Or open in browser:
```
https://your-app.vercel.app/api/v1/health
```

### Step 6: Add Custom Domain (Optional)

1. Vercel Dashboard → **Settings** → **Domains**
2. Enter: `app.pozi.com.na`
3. Click **"Add"**
4. Vercel will show DNS records to add
5. Add those records to your domain's DNS
6. Wait for SSL (automatic, takes a few minutes)

## 🎉 You're Done!

Your API is now:
- ✅ On GitHub (version controlled)
- ✅ Deployed on Vercel (live on internet)
- ✅ Auto-deploying on every push

## 🔄 Future Updates

```bash
# Make changes
# ... edit files ...

# Commit and push
git add .
git commit -m "Update API"
git push

# Vercel automatically deploys!
```

## 📝 Quick Reference

**GitHub Repo:** `https://github.com/YOUR_USERNAME/collexia-api`  
**Vercel URL:** `https://your-app.vercel.app`  
**API Base:** `https://your-app.vercel.app/api/v1`

---

**Ready?** Start with Step 1 - Create GitHub repository!

