# Postman Collection Setup Guide

## Quick Start

1. **Import Collection**: Import `Collexia_API.postman_collection.json` into Postman
2. **Import Environment**: Import `Collexia_API.postman_environment.json` into Postman
3. **Select Environment**: Select "Collexia API - UAT" environment in Postman
4. **Start Testing**: All endpoints are ready to use!

---

## Files Included

1. **Collexia_API.postman_collection.json** - Complete API collection with all endpoints
2. **Collexia_API.postman_environment.json** - Environment variables for UAT

---

## Collection Structure

### 📁 System
- Health Check

### 📁 Students
- List All Students
- Create/Update Student
- Get Student by ID

### 📁 Properties
- List All Properties
- Create/Update Property
- Get Property by Code

### 📁 Mandates
- Register Mandate
- Check Mandate Status
- Get Mandate Details
- Cancel Mandate

### 📁 Payments
- Download Payment History
- Get Payments by Student
- Get Payments by Contract

### 📁 Collexia Direct API
- Mandate Final Fate (Direct Collexia call)
- Download Payments (Direct Collexia call)

---

## Environment Variables

The environment file includes:

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | http://localhost | Your local API URL |
| `collexia_base_url` | https://collection-uat.collexia.co | Collexia UAT endpoint |
| `collexia_username` | bareinvuat | Collexia username |
| `collexia_password` | [Your Password] | Collexia password |
| `collexia_client_id` | 6FA41D83-B8A5-11F0-B138-42010A960205 | Client ID |
| `collexia_client_secret` | [Your Secret] | Client secret |
| `collexia_merchant_gid` | 12584 | Merchant GID |
| `collexia_remote_gid` | 71 | Remote GID |
| `test_contract_reference` | 31281117385028 | Example contract ref |
| `uat_active_account` | 62001543455 | UAT test account (Active) |
| `uat_closed_account` | 62001871799 | UAT test account (Closed) |

---

## Important Notes

### HMAC Signature for Direct Collexia Calls

For direct Collexia API calls, the `CX_SWITCH_HSH` header needs to be calculated:

1. **Concatenate**: `ClientID + DTS`
   - Example: `"6FA41D83-B8A5-11F0-B138-42010A960205" + "2025-11-17 12:19:39.740"`

2. **Compute HMAC-SHA512**: Using Client Secret
   - `hmac_sha512(client_secret, message)`

3. **Base64 Encode**: The HMAC result

4. **Set Header**: `CX_SWITCH_HSH: [base64_encoded_hmac]`

**Note**: Postman doesn't have built-in HMAC-SHA512, so you have two options:

**Option 1**: Use our API endpoints (recommended)
- Use the endpoints in "Mandates" and "Payments" folders
- Our API handles HMAC calculation automatically

**Option 2**: Calculate manually or use Postman Pre-request Script
- You can add a pre-request script to calculate HMAC
- Or calculate it server-side and set manually

---

## Testing Workflow

### 1. Setup Test Data

1. **Create a Student**:
   ```
   POST /api/v1/students
   Use UAT test account: 62001543455
   ```

2. **Create a Property**:
   ```
   POST /api/v1/properties
   ```

### 2. Register a Mandate

```
POST /api/v1/mandates/register
{
  "student_id": "STU001",
  "property_id": 1,
  "monthly_rent": 100.00,
  "start_date": "20250101",
  "frequency_code": 4,
  "no_of_installments": 12,
  "tracking_days": 3,
  "mag_id": 46
}
```

### 3. Check Mandate Status

```
POST /api/v1/mandates/status
{
  "contract_reference": "31281117385028"
}
```

### 4. Download Payments

```
POST /api/v1/payments/download
```

---

## UAT Test Accounts

Use these test accounts from the test pack:

| Status | Account Numbers |
|--------|----------------|
| **Active** | 62001543455, 62001543405, 62001543398 |
| **Inactive** | 62001623380, 62002288539 |
| **Closed** | 62001871799, 62003081560 |
| **Frozen** | 62001914812 |

---

## Tips for Meeting with Collexia Team

1. **Start with Health Check** - Verify API is running
2. **Show Student/Property Creation** - Demonstrate basic operations
3. **Register a Mandate** - Use UAT active account (62001543455)
4. **Check Mandate Status** - Show status retrieval
5. **Download Payments** - Demonstrate payment history
6. **Test Error Scenarios** - Show closed account rejection

---

## Troubleshooting

### If requests fail:

1. **Check Environment**: Make sure "Collexia API - UAT" is selected
2. **Verify base_url**: Should be `http://localhost` or your server URL
3. **Check API is running**: Test health endpoint first
4. **Verify credentials**: Check environment variables are set correctly

### For Direct Collexia Calls:

- HMAC signature must be calculated correctly
- Timestamp must be in ISO 8601 format with milliseconds
- All headers must be present: `CX_SWITCH_ClientId`, `CX_SWITCH_DTS`, `CX_SWITCH_HSH`

---

## Export/Import Instructions

### Import into Postman:

1. Open Postman
2. Click **Import** button (top left)
3. Select **File** tab
4. Choose `Collexia_API.postman_collection.json`
5. Click **Import**
6. Repeat for `Collexia_API.postman_environment.json`
7. Select the environment from dropdown (top right)

### Share with Team:

- Export collection: Right-click collection → Export
- Export environment: Click environment → Export
- Share the JSON files

---

**Ready to test!** 🚀

