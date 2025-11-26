<?php

/**
 * CollexiaClient
 *
 * A lightweight PHP client for interacting with the Collexia REST APIs.  This class
 * handles HTTP Basic Authentication, HMAC‑based digital signatures, mutual TLS
 * configuration and automatic retries for transient failures.  It exposes
 * high‑level methods for the core Collexia operations: mandate management,
 * payment history download and enhanced credit transfers.
 */
class CollexiaClient
{
    /**
     * Configuration options.
     *
     * Recognised keys:
     *  - base_url: the base domain for Collexia (e.g. https://collection-uat.collexia.co)
     *  - base_path: the REST base path (e.g. /api/coswitchuadsrest/v3)
     *  - basic_user: username for HTTP Basic Auth
     *  - basic_pass: password for HTTP Basic Auth
     *  - header_prefix: prefix for custom HMAC headers (default X-COLLEXIA)
     *  - client_id: unique client identifier assigned by Collexia
     *  - client_secret: secret used to compute HMAC
     *  - merchant_gid: merchant global identifier
     *  - remote_gid: remote global identifier
     *  - cert_path: path to client certificate (PEM)
     *  - key_path: path to client private key (PEM)
     *  - pfx_path: path to client certificate in PFX/P12 format
     *  - pfx_pass: passphrase for the PFX file
     */
    private $config;

    /**
     * Construct a new client instance.
     *
     * @param array $config Configuration options. Missing entries fall back to
     *                      environment variables (see README for names).
     */
    public function __construct(array $config = [])
    {
        // Merge environment variables into configuration with user supplied values
        $envMap = [
            'base_url'      => 'COLLEXIA_BASE_URL',
            'base_path'     => 'COLLEXIA_BASE_PATH',
            'basic_user'    => 'COLLEXIA_BASIC_USER',
            'basic_pass'    => 'COLLEXIA_BASIC_PASS',
            'header_prefix' => 'COLLEXIA_HEADER_PREFIX',
            'client_id'     => 'COLLEXIA_CLIENT_ID',
            'client_secret' => 'COLLEXIA_CLIENT_SECRET',
            'merchant_gid'  => 'COLLEXIA_MERCHANT_GID',
            'remote_gid'    => 'COLLEXIA_REMOTE_GID',
            'cert_path'     => 'COLLEXIA_TLS_CERT_PATH',
            'key_path'      => 'COLLEXIA_TLS_KEY_PATH',
            'pfx_path'      => 'COLLEXIA_TLS_PFX_PATH',
            'pfx_pass'      => 'COLLEXIA_TLS_PFX_PASS',
        ];

        foreach ($envMap as $key => $envVar) {
            if (!array_key_exists($key, $config) || $config[$key] === null) {
                $envValue = getenv($envVar);
                if ($envValue !== false) {
                    $config[$key] = $envValue;
                }
            }
        }
        // Provide sensible defaults
        if (!isset($config['header_prefix']) || $config['header_prefix'] === '') {
            $config['header_prefix'] = 'X-COLLEXIA';
        }
        $this->config = $config;
    }

    /**
     * Build HTTP headers for a request including Basic Auth and HMAC signature.
     *
     * @return array List of header strings.
     */
    private function buildHeaders(): array
    {
        $prefix = $this->config['header_prefix'];
        $clientId = $this->config['client_id'];
        $secret = $this->config['client_secret'];
        $dts = $this->nowTimestamp();

        // Compute HMAC over client ID + timestamp, base64 encoded
        $msg = $clientId . $dts;
        $hmac = hash_hmac('sha512', $msg, $secret, true);
        $hsh = base64_encode($hmac);

        // Basic auth header
        $basicUser = $this->config['basic_user'];
        $basicPass = $this->config['basic_pass'];
        $basic = base64_encode("{$basicUser}:{$basicPass}");

        return [
            "Authorization: Basic {$basic}",
            "{$prefix}_ClientId: {$clientId}",
            "{$prefix}_DTS: {$dts}",
            "{$prefix}_HSH: {$hsh}",
            'Content-Type: application/json',
            'Accept: application/json',
        ];
    }

    /**
     * Format current time with milliseconds as required by the spec.
     *
     * @return string DateTime in ISO format with milliseconds.
     */
    private function nowTimestamp(): string
    {
        $micro = microtime(true);
        $ts = \DateTimeImmutable::createFromFormat('U.u', sprintf('%.6f', $micro));
        return $ts->format('Y-m-d H:i:s.v');
    }

    /**
     * Initialise a cURL handle with TLS and other settings.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path   Relative API path (without base URL/base path)
     * @param array  $body   Associative array to JSON encode as request body
     * @param string|null $idempotencyKey Optional idempotency header value
     *
     * @return resource cURL handle
     */
    private function initCurl(string $method, string $path, array $body = [], ?string $idempotencyKey = null)
    {
        $url = rtrim($this->config['base_url'], '/') . rtrim($this->config['base_path'], '/') . '/' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Build base headers
        $headers = $this->buildHeaders();
        if ($idempotencyKey) {
            $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) {
            $json = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }
        // Enforce TLS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // Configure client certificate and key if provided
        if (!empty($this->config['pfx_path'])) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->config['pfx_path']);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
            if (!empty($this->config['pfx_pass'])) {
                curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->config['pfx_pass']);
            }
        } elseif (!empty($this->config['cert_path']) && !empty($this->config['key_path'])) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->config['cert_path']);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->config['key_path']);
            // Optionally set key password if included in key path
            if (!empty($this->config['pfx_pass'])) {
                curl_setopt($ch, CURLOPT_KEYPASSWD, $this->config['pfx_pass']);
            }
        }
        return $ch;
    }

    /**
     * Execute the cURL request and return a structured response.
     *
     * Automatically retries up to 3 times on 5xx or 429 responses with exponential backoff.
     *
     * @param resource $ch cURL handle
     *
     * @return array Associative array with keys: ok (bool), status (int), data (mixed), error (mixed)
     */
    private function executeRequest($ch): array
    {
        $attempt = 0;
        do {
            $attempt++;
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            // Parse JSON response if possible
            $data = null;
            if ($response !== false && strlen($response) > 0) {
                $decoded = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $decoded;
                } else {
                    $data = $response;
                }
            }
            // On success (2xx) or client error (4xx) break
            if ($status >= 200 && $status < 300) {
                return ['ok' => true, 'status' => $status, 'data' => $data];
            }
            if ($status >= 400 && $status < 500 && $status !== 429) {
                return ['ok' => false, 'status' => $status, 'error' => $data ?: $err];
            }
            // For 429 or 5xx, retry with exponential backoff
            if ($attempt < 3 && ($status === 429 || $status >= 500)) {
                usleep(300000 * $attempt); // 300ms, 600ms
                continue;
            }
            break;
        } while ($attempt < 3);
        // Final failure
        return ['ok' => false, 'status' => $status, 'error' => $data ?: $err ?: 'Unknown error'];
    }

    /**
     * Send a POST request to the given API path.
     *
     * @param string $path API path (e.g. /mandates/load)
     * @param array $body Request body
     * @param string|null $idempotencyKey Optional idempotency key
     *
     * @return array Response structure
     */
    public function post(string $path, array $body, ?string $idempotencyKey = null): array
    {
        $ch = $this->initCurl('POST', $path, $body, $idempotencyKey);
        $result = $this->executeRequest($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Convenience method: load a new mandate (EnDO registration).
     *
     * @param array $mandateData The mandate object. Must include messageInfo and mandate fields.
     * @param string|null $idempotencyKey Optional idempotency key, recommended to use contractReference
     *
     * @return array Response structure
     */
    public function loadMandate(array $mandateData, ?string $idempotencyKey = null): array
    {
        return $this->post('/mandates/load', $mandateData, $idempotencyKey);
    }

    /**
     * Convenience method: request final fate for a loaded mandate.
     *
     * @param array $payload Payload containing contractReference and other required fields.
     *
     * @return array Response structure
     */
    public function mandateFinalFate(array $payload): array
    {
        return $this->post('/mandates/finalfate', $payload);
    }

    /**
     * Download payment history.
     *
     * @param array $payload Payload containing merchantGid, remoteGid, frontEndUserName.
     *
     * @return array Response structure
     */
    public function downloadPayments(array $payload): array
    {
        return $this->post('/paymenthistory/download', $payload);
    }

    /**
     * Initiate an Enhanced Credit (EnCr) transfer.
     *
     * @param array $payload Transfer payload as per the specification.
     * @param string|null $idempotencyKey Optional idempotency key, recommended to use remoteReferenceNo
     *
     * @return array Response structure
     */
    public function encrTransfer(array $payload, ?string $idempotencyKey = null): array
    {
        return $this->post('/encr/transfer', $payload, $idempotencyKey);
    }

    /**
     * Request the current or final fate of an EnCr transfer.
     *
     * @param array $payload Info request payload as per the specification.
     *
     * @return array Response structure
     */
    public function encrInfo(array $payload): array
    {
        return $this->post('/encr/info', $payload);
    }
}