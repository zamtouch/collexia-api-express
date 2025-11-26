# 🚀 Next Steps - Deploy Your API

## ✅ What We've Done

- ✅ Created Express API
- ✅ Tested all endpoints
- ✅ Same Collexia config as PHP version
- ✅ Ready for deployment

## 📋 Deployment Checklist

### Step 1: Initialize Git (if not done)

```bash
git init
git add .
git commit -m "Add Express API with Collexia integration"
```

### Step 2: Create GitHub Repository

1. Go to [github.com](https://github.com)
2. Click **"New repository"**
3. Name: `collexia-api` (or your choice)
4. **Don't** initialize with README
5. Click **"Create repository"**

### Step 3: Push to GitHub

```bash
# Add remote (replace YOUR_USERNAME and REPO_NAME)
git remote add origin https://github.com/YOUR_USERNAME/REPO_NAME.git

# Push
git branch -M main
git push -u origin main
```

### Step 4: Deploy to Vercel

**Option A: Vercel CLI (Quick)**

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel

# Follow prompts:
# - Set up and deploy? Yes
# - Which scope? (select your account)
# - Link to existing project? No
# - Project name? collexia-api
# - Directory? ./
# - Override settings? No
```

**Option B: GitHub Integration (Recommended)**

1. Go to [vercel.com](https://vercel.com)
2. Sign up/Login with GitHub
3. Click **"Add New Project"**
4. Import your GitHub repository
5. Vercel auto-detects Express
6. Click **"Deploy"**

### Step 5: Set Environment Variables in Vercel

After deployment:

1. Vercel Dashboard → Your Project → **Settings** → **Environment Variables**
2. Add these (or use defaults):

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

3. Click **"Save"**
4. Redeploy (or it auto-redeploys)

### Step 6: Test Deployed API

```bash
# Replace with your Vercel URL
curl https://your-app.vercel.app/api/v1/health
```

### Step 7: Add Custom Domain (Optional)

1. Vercel Dashboard → **Settings** → **Domains**
2. Add `app.pozi.com.na`
3. Update DNS records as shown
4. Wait for SSL (automatic)

## 🎯 Quick Commands

```bash
# 1. Initialize Git
git init
git add .
git commit -m "Add Express API"

# 2. Create GitHub repo (do this on github.com first)
git remote add origin https://github.com/YOUR_USERNAME/REPO_NAME.git
git push -u origin main

# 3. Deploy to Vercel
npm i -g vercel
vercel

# 4. Test deployed API
curl https://your-app.vercel.app/api/v1/health
```

## 📝 What Happens Next

1. **GitHub** - Your code is version controlled
2. **Vercel** - Your API is live on the internet
3. **Automatic Deployments** - Every push to GitHub auto-deploys
4. **Custom Domain** - Add `app.pozi.com.na` when ready

## 🔄 Future Updates

After initial deployment:

```bash
# Make changes
# ... edit files ...

# Commit and push
git add .
git commit -m "Update API"
git push

# Vercel automatically deploys!
```

## ⚠️ Important Notes

- **Environment Variables** - Set them in Vercel dashboard
- **No Database** - Currently using in-memory storage (data resets on restart)
- **Collexia API** - Uses UAT environment (change to production when ready)
- **CORS** - Already configured for your domains

## 🎉 You're Ready!

Your API is tested and working. Now let's get it deployed!

---

**Ready to deploy?** Let's start with Git!

