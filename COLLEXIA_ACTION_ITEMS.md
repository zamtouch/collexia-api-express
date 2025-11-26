# Action Items for Collexia Team

## Summary

This document outlines the specific action items we need from the Collexia team to **unblock our integration**. Currently, we **cannot complete any real API calls** to Collexia UAT due to authentication failures. We need these items resolved before we can begin actual testing.

---

## 🔴 High Priority Items

### 1. TLS Client Certificates

**What We Need:**
- Client certificate files for mutual TLS authentication
- Certificate format (PEM, PFX/P12)
- Any required passphrases
- Hostname requirements for certificate generation

**Why It's Needed:**
- Required for full authentication with Collexia API
- **Currently BLOCKING all API operations** - we cannot make any successful calls
- Security requirement per API documentation
- Without this, we get 401 authentication errors on every request

**Status:** ⏳ Awaiting certificates from Collexia - **URGENT - BLOCKING ALL TESTING**

---

### 2. IP Address Whitelisting

**What We Need:**
- Confirmation if IP whitelisting is required for UAT
- Whitelisting of our server IP addresses:
  - Development Server: [To be provided]
  - Production Server: [To be provided when available]

**Why It's Needed:**
- **May be blocking API requests** - could be causing authentication failures
- Security requirement per API documentation
- Without this, our requests may be rejected even with correct credentials

**Status:** ⏳ Awaiting confirmation and IP whitelisting - **URGENT - BLOCKING ALL TESTING**

---

## 🟡 Medium Priority Items

### 3. Account Activation Verification

**What We Need:**
- Verification that UAT account `bareinvuat` is fully activated
- Confirmation that Merchant GID `12584` (XXINTEGR) is active
- Verification that Remote GID `71` is correctly associated
- Any additional setup steps required

**Why It's Needed:**
- **Receiving authentication error on every API call**
- Need to confirm account is ready for use
- This error is preventing ALL API operations

**Status:** ⏳ Awaiting verification from Collexia - **URGENT - BLOCKING ALL TESTING**

**Error Received (on every API call):**
```
ERROR #5813: Null oid, class 'Auth0.Application'
HTTP Status: 401 (Unauthorized)
```

**Impact**: We cannot:
- Register any mandates
- Retrieve any data
- Download payment history
- Test any functionality

---

## 🟢 Low Priority Items

### 4. Test Data and Documentation

**What We Need:**
- Sample contract references for testing
- Test account numbers for UAT environment
- Recommended test scenarios
- Any additional documentation or examples

**Why It's Needed:**
- To facilitate thorough testing
- To ensure we're testing correctly

**Status:** ℹ️ Information request

---

## Quick Reference

**Our Configuration:**
- Username: `bareinvuat`
- Merchant GID: `12584` (XXINTEGR)
- Remote GID: `71`
- Client ID: `6FA41D83-B8A5-11F0-B138-42010A960205`
- UAT Endpoint: `https://collection-uat.collexia.co/api/coswitchuadsrest/v3`

**Contact:**
- [Your Name]
- [Your Email]
- [Your Phone]

---

**Last Updated:** November 17, 2025

