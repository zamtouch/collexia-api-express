# Quick Testing Guide

## ✅ Your API is Running!

Server: `http://localhost:3000`

## 🚀 3 Ways to Test

### 1. Automated Test (Easiest)

```bash
node test-endpoints.js
```

This runs all tests automatically with colored output.

### 2. Simple Test

```bash
npm test
```

### 3. Manual Testing

#### Health Check
```bash
curl http://localhost:3000/api/v1/health
```

#### Create Student
```bash
curl -X POST http://localhost:3000/api/v1/students ^
  -H "Content-Type: application/json" ^
  -d "{\"student_id\":\"STU001\",\"full_name\":\"John Doe\",\"email\":\"john@example.com\",\"account_number\":\"123456789\",\"bank_id\":65}"
```

#### Get Student
```bash
curl http://localhost:3000/api/v1/students/STU001
```

## 📊 Test Results

When you run `node test-endpoints.js`, you'll see:

- ✅ Health Check
- ✅ Create Student  
- ✅ Get Student
- ✅ List Students
- ✅ Create Property
- ✅ Get Property
- ✅ List Properties
- ⚠️ Register Mandate (calls real Collexia API)

## 🎯 What's Working

✅ All basic endpoints  
✅ In-memory storage  
✅ Same Collexia config as PHP  
✅ CORS configured  
✅ Error handling  

## 📝 Full Testing Guide

See `HOW_TO_TEST.md` for complete testing instructions.

---

**Ready to test?** Run: `node test-endpoints.js`

