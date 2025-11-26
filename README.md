# Collexia PHP Integration

This package provides a PHP‑centric template for integrating with Collexia's Enhanced Debit Order (EnDO) and Enhanced Credit (EnCr) REST APIs.  It contains:

- **src/CollexiaClient.php** – a lightweight client class that handles HTTP Basic authentication, HMAC‑SHA512 digital signature headers, mutual TLS configuration and automatic retries.  It exposes convenient methods for mandate registration, final fate look‑ups, payment history downloads and Enhanced Credit transfers.
- **api/** – A complete REST API implementation for managing student rental payments through Collexia. This API can be used by Next.js and React Native Expo applications.
- **config.php** – an example configuration file that sources values from environment variables.  You can copy this into your project or adjust the values directly.
- **docs/** – plain‑text versions of the official Collexia specifications and the extracted swagger definitions (see below).  These files make it easy for tools like Cursor to index the spec while you work.

## Quick Start - REST API

For a ready-to-use REST API that works with Next.js and React Native Expo apps, see the `api/` directory:

1. **Setup**: Follow the instructions in `SETUP.md`
2. **API Documentation**: See `api/README.md` for endpoint documentation
3. **Test**: Run `php api/test-examples.php` to test the API

The REST API provides endpoints for:
- Student/tenant management
- Property management
- Mandate registration (rent payment setup)
- Payment history tracking
- Mandate status checking and cancellation

## Getting started

1. **Install prerequisites**: You need PHP 8.1 or later with the cURL extension enabled.  Ensure your server is configured to support **mutual TLS** (client certificate authentication).
2. **Add your certificates**: Place your client certificate and private key in a secure location on disk.  Set `COLLEXIA_TLS_CERT_PATH` and `COLLEXIA_TLS_KEY_PATH` (or `COLLEXIA_TLS_PFX_PATH` and `COLLEXIA_TLS_PFX_PASS` if you have a PFX file) in your environment or edit `config.php` accordingly.  The security requirements are detailed in the API security document【690433214325054†L34-L70】.
3. **Configure authentication**: Set `COLLEXIA_BASIC_USER` and `COLLEXIA_BASIC_PASS` for HTTP Basic authentication, and `COLLEXIA_CLIENT_ID` and `COLLEXIA_CLIENT_SECRET` for HMAC signing.  These values are provided by Collexia during onboarding.  Each request will include headers like `X‑COLLEXIA_CLIENTID`, `X‑COLLEXIA_DTS` and `X‑COLLEXIA_HSH`【690433214325054†L92-L158】.
4. **Load configuration and instantiate the client**:

```php
<?php
require 'src/CollexiaClient.php';
$config = require 'config.php';
$client = new CollexiaClient($config);
```

5. **Prepare request payloads**: Each method expects an associative array matching the JSON structures defined in the official spec.  For example, to load a mandate:

```php
$now = new \DateTimeImmutable();
$messageDate = $now->format('Ymd');
$messageTime = $now->format('His');

$payload = [
    'messageInfo' => [
        'merchantGid'      => (int)$config['merchant_gid'],
        'remoteGid'        => (int)$config['remote_gid'],
        'messageDate'      => $messageDate,
        'messageTime'      => $messageTime,
        'systemUserName'   => $config['basic_user'],
        'frontEndUserName' => 'php',
    ],
    'mandate' => [
        'clientNo'                => 'S123',
        'userReference'           => 'MyTenant',
        'frequencyCode'           => 12,
        'installmentAmount'       => 1500.00,
        'noOfInstallments'        => 12,
        'origin'                  => (int)$config['remote_gid'],
        'contractReference'       => 'MAND001',
        'magId'                   => 1,
        'initialAmount'           => 0.00,
        'firstCollectionDate'     => '20250110',
        'collectionDay'           => 10,
        'numberOfTrackingDays'    => 0,
        'debtorAccountName'       => 'John Doe',
        'debtorIdentificationType'=> 1,
        'debtorIdentificationNo'  => '1234567890',
        'debtorAccountNumber'     => '62123456789',
        'debtorAccountType'       => 1,
        'debtorBanId'             => 2,
    ],
];

$response = $client->loadMandate($payload, $payload['mandate']['contractReference']);
if ($response['ok']) {
    // handle success
} else {
    // handle error
}
```

Similarly, you can call `$client->downloadPayments($payload)` or `$client->encrTransfer($payload)` as required.  See the plain‑text specs in `docs/` for field names and allowable values【254031227609699†L162-L180】.

## About the documentation files

The `docs/` directory contains text extracts of the Collexia API specifications and the OpenAPI swagger definitions used to generate API clients.  These are included so that your IDE and tools like Cursor can reference them directly.  The main documents are:

| File | Purpose |
| --- | --- |
| **CO_JSON_REST_API_Interface_Specification_V3_0.txt** | Defines the REST API for Enhanced Debit Orders (mandates) and instalments, including operations such as *load*, *final fate*, *enquiry*, *cancel*, *installment request/update/cancel* and *download payments*【254031227609699†L162-L190】. |
| **CO_JSON_REST_API_Interface_Specification_EnCr_V3_00.txt** | Describes the Enhanced Credit (EnCr) API, its message flow and the two endpoints (transfer and transfer info)【372355305295873†L90-L103】. |
| **CO_API_Security_Standards_Third_Party_Ver_1_01.txt** | Explains the security requirements: HTTP Basic authentication, mutual TLS, HMAC digital signatures and IP whitelisting【690433214325054†L34-L70】【690433214325054†L92-L158】. |
| **docs/swagger/** | Contains JSON swagger files for the mandates, payment history and EnCr endpoints. |

Use these references alongside the PHP client to ensure your requests adhere to the required structure and that you handle responses correctly.

## Next steps

Once you have loaded mandates and initiated collections, you can automate reconciliation by scheduling periodic calls to `downloadPayments()` and updating your system accordingly.  For payouts, use `encrTransfer()` followed by `encrInfo()` to monitor the final fate of each transfer.  Remember to log every request and response for auditability and use idempotency keys to prevent duplicate submissions.  With this PHP package and the included documentation, you can integrate with Collexia confidently and comply with their security standards.