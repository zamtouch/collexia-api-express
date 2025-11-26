# Postman Collection - Quick Start Guide

## 🚀 Import Instructions

### Step 1: Import Collection
1. Open Postman
2. Click **Import** (top left)
3. Drag and drop `Collexia_API.postman_collection.json` OR click "Upload Files" and select it
4. Click **Import**

### Step 2: Import Environment
1. Click **Import** again
2. Drag and drop `Collexia_API.postman_environment.json` OR click "Upload Files" and select it
3. Click **Import**

### Step 3: Select Environment
1. Look at the top right of Postman
2. Click the environment dropdown (should say "No Environment")
3. Select **"Collexia API - UAT"**

### Step 4: Update Base URL (if needed)
1. Click the eye icon next to the environment dropdown
2. Click **Edit** next to "Collexia API - UAT"
3. If your API is not on `localhost`, update the `base_url` variable
4. Click **Save**

---

## ✅ Quick Test Sequence

### 1. Health Check
- Open: **System → Health Check**
- Click **Send**
- Should return: `{"success": true, "message": "API is running"}`

### 2. Create Student
- Open: **Students → Create/Update Student**
- The request body already has a UAT test account (`62001543455`)
- Click **Send**
- Note the `student_id` from the response

### 3. Create Property
- Open: **Properties → Create/Update Property**
- Click **Send**
- Note the `property_id` from the response

### 4. Register Mandate
- Open: **Mandates → Register Mandate**
- Update `student_id` and `property_id` if needed
- Click **Send**
- ✅ **This will register a mandate with Collexia!**
- The `contract_reference` will be auto-saved to environment

### 5. Check Mandate Status
- Open: **Mandates → Check Mandate Status**
- The `contract_reference` is already filled from step 4
- Click **Send**
- Should show: `mandateLoaded: true`

### 6. Download Payments
- Open: **Payments → Download Payment History**
- Click **Send**
- ✅ **This calls Collexia API directly!**

---

## 📋 Collection Features

### ✅ Auto-Save Variables
The collection automatically saves:
- `last_student_id` - After creating a student
- `last_property_id` - After creating a property
- `last_contract_reference` - After registering a mandate

These are used in subsequent requests automatically!

### ✅ Test Scripts
Each request includes test scripts that:
- Verify response status codes
- Check response structure
- Auto-save IDs for later use

### ✅ Example Responses
Many requests include example responses so you can see what to expect.

---

## 🎯 Meeting Demo Flow

**Recommended order for your meeting with Collexia:**

1. **Health Check** - Show API is running
2. **Create Student** - Show student management
3. **Create Property** - Show property management
4. **Register Mandate** - ⭐ **Main demo** - Register with UAT account
5. **Check Status** - Show mandate status retrieval
6. **Download Payments** - Show payment history download

**Total time: ~5-10 minutes**

---

## 🔧 Environment Variables

All credentials are pre-configured in the environment:

| Variable | Value | Notes |
|----------|-------|-------|
| `base_url` | http://localhost | Your API server |
| `collexia_base_url` | https://collection-uat.collexia.co | Collexia UAT |
| `collexia_username` | bareinvuat | ✅ Set |
| `collexia_password` | [Your Password] | ✅ Set |
| `collexia_client_id` | 6FA41D83-B8A5-11F0-B138-42010A960205 | ✅ Set |
| `collexia_client_secret` | [Your Secret] | ✅ Set |
| `uat_active_account` | 62001543455 | UAT test account |

---

## 💡 Tips

1. **Use the Collection Runner** for automated testing:
   - Click the collection name
   - Click "Run"
   - Select requests to run
   - Click "Run Collexia Rental Payment API"

2. **View Test Results**:
   - After sending a request, check the "Test Results" tab
   - Green checkmarks = tests passed

3. **Share with Team**:
   - Right-click collection → Export
   - Share the JSON file

---

## 🐛 Troubleshooting

**Request fails?**
- Check environment is selected (top right)
- Verify `base_url` is correct
- Make sure your API server is running
- Check the Console tab for errors

**Can't connect?**
- Test Health Check first
- Verify your server URL is correct
- Check CORS settings if calling from browser

---

**Ready to test!** 🎉




