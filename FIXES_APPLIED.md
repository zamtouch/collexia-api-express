# Fixes Applied Based on Collexia Feedback

## Date: November 17, 2025

## Issues Identified by Collexia

1. **Wrong Header Prefix**: We were using `X-COLLEXIA` but should use `CX_SWITCH`
2. **Wrong Header Name**: Should be `CX_SWITCH_ClientId` (not `CX_SWITCH_CLIENTID`)

## Fixes Applied

### 1. Updated Header Prefix
- **File**: `config.php`
- **Change**: Updated `header_prefix` from `X-COLLEXIA` to `CX_SWITCH`
- **Line**: 16

### 2. Updated Header Name Format
- **File**: `src/CollexiaClient.php`
- **Change**: Updated header name from `{$prefix}_CLIENTID` to `{$prefix}_ClientId`
- **Line**: 98

## Current Header Format

Headers now generated correctly:
- `Authorization: Basic [base64 encoded username:password]`
- `CX_SWITCH_ClientId: 6FA41D83-B8A5-11F0-B138-42010A960205`
- `CX_SWITCH_DTS: 2025-11-17 12:19:39.740` (ISO 8601 format with milliseconds)
- `CX_SWITCH_HSH: [base64 encoded HMAC-SHA512 signature]`
- `Content-Type: application/json`
- `Accept: application/json`

## HMAC Signature Calculation

The HMAC signature is calculated as:
1. Concatenate: `ClientID + DTS` (e.g., "6FA41D83-B8A5-11F0-B138-42010A960205" + "2025-11-17 12:19:39.740")
2. Compute HMAC-SHA512 using Client Secret
3. Base64 encode the result
4. Set as `CX_SWITCH_HSH` header value

## Next Steps

1. ✅ Header prefix fixed
2. ✅ Header name format fixed
3. ⏳ Wait for new Client Secret (Collexia will re-send via WhatsApp)
4. ⏳ Test API calls again once new secret is received
5. ⏳ Review test pack document: `Bare Investment_Integration Standard Test Planning V3.0.xlsx`

## Notes from Collexia

- **No IP Whitelisting Required**: Collexia confirmed IP whitelisting is not necessary
- **No TLS Certificates Required**: Not mentioned in their response, so may not be needed
- **Test Pack Available**: Test cases and account numbers are in the test pack document
- **Client Secret**: Will be re-sent via WhatsApp


