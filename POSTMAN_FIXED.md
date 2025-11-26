# Postman Collection - Fixed Base URL

## ✅ Issue Fixed

The routing issue has been resolved. The problem was that the project is in `htdocs/collexia`, so the base URL needs to include `/collexia`.

## Updated Files

1. **Collexia_API.postman_environment.json** - Base URL updated to `http://localhost/collexia`
2. **Collexia_API.postman_collection.json** - Base URL updated
3. **.htaccess** - Fixed rewrite rules
4. **api/router.php** - Fixed path handling

## Correct Base URL

**Base URL**: `http://localhost/collexia`

## All Endpoints Now Work

- ✅ `http://localhost/collexia/api/v1/health`
- ✅ `http://localhost/collexia/api/v1/students`
- ✅ `http://localhost/collexia/api/v1/properties`
- ✅ `http://localhost/collexia/api/v1/mandates/*`
- ✅ `http://localhost/collexia/api/v1/payments/*`

## Postman Setup

1. **Re-import the environment file** (or update manually):
   - Open Postman
   - Click on "Collexia API - UAT" environment
   - Edit `base_url` variable
   - Change from `http://localhost` to `http://localhost/collexia`
   - Save

2. **Test Health Check**:
   - Open: System → Health Check
   - Click Send
   - Should return: `{"success": true, "message": "API is running"}`

## Quick Test

```bash
curl http://localhost/collexia/api/v1/health
```

Expected response:
```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2025-11-19 05:29:49"
}
```

---

**Status**: ✅ Fixed and ready for your meeting!




