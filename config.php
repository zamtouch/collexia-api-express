<?php

// Configuration file for Collexia integration
//
// This script returns an array of configuration values used by CollexiaClient. It
// sources values from environment variables where possible and falls back to
// sensible defaults.  Copy this file to your project and adjust the values or
// override them using environment variables in your deployment.  See the
// README for details on each parameter.

return [
    'base_url'      => getenv('COLLEXIA_BASE_URL') ?: 'https://collection-uat.collexia.co',
    'base_path'     => getenv('COLLEXIA_BASE_PATH') ?: '/api/coswitchuadsrest/v3',
    'basic_user'    => getenv('COLLEXIA_BASIC_USER') ?: 'bareinvuat',
    'basic_pass'    => getenv('COLLEXIA_BASIC_PASS') ?: 'Ms@utbinT!11',
    'header_prefix' => getenv('COLLEXIA_HEADER_PREFIX') ?: 'CX_SWITCH',
    'client_id'     => getenv('COLLEXIA_CLIENT_ID') ?: '6FA41D83-B8A5-11F0-B138-42010A960205',
    'client_secret' => getenv('COLLEXIA_CLIENT_SECRET') ?: '9FXhhuOtjiKinPFpbnSb',
    'merchant_gid'  => getenv('COLLEXIA_MERCHANT_GID') ?: 12584,
    'remote_gid'    => getenv('COLLEXIA_REMOTE_GID') ?: 71,
    'origin'        => getenv('COLLEXIA_ORIGIN') ?: 71,
    'cert_path'     => getenv('COLLEXIA_TLS_CERT_PATH') ?: '',
    'key_path'      => getenv('COLLEXIA_TLS_KEY_PATH') ?: '',
    'pfx_path'      => getenv('COLLEXIA_TLS_PFX_PATH') ?: '',
    'pfx_pass'      => getenv('COLLEXIA_TLS_PFX_PASS') ?: '',
];