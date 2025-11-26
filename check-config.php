<?php

/**
 * Check Collexia Configuration
 */

require 'config.php';
$config = require 'config.php';

echo "=== Collexia UAT Configuration Check ===\n\n";

echo "✅ Configured Settings:\n";
echo "   Base URL: " . $config['base_url'] . "\n";
echo "   Base Path: " . $config['base_path'] . "\n";
echo "   Username: " . $config['basic_user'] . "\n";
echo "   Merchant GID: " . $config['merchant_gid'] . "\n";
echo "   Remote GID: " . $config['remote_gid'] . "\n";
echo "   Origin: " . ($config['origin'] ?? 'Not set') . "\n";
echo "   Client ID: " . $config['client_id'] . "\n";
echo "   Header Prefix: " . $config['header_prefix'] . "\n\n";

echo "⚠️  Required Settings (from WhatsApp):\n";
echo "   Password: " . (!empty($config['basic_pass']) ? "✓ Set" : "✗ NOT SET - Add to config.php") . "\n";
echo "   Client Secret: " . (!empty($config['client_secret']) ? "✓ Set" : "✗ NOT SET - Add to config.php") . "\n\n";

if (empty($config['basic_pass']) || empty($config['client_secret'])) {
    echo "📝 Action Required:\n";
    echo "   1. Open config.php\n";
    echo "   2. Add your password on line 15\n";
    echo "   3. Add your client secret on line 18\n";
    echo "   (Both were sent via WhatsApp to +264 81 850 7476)\n\n";
} else {
    echo "✅ All credentials are configured!\n";
    echo "   You can now test the API with Collexia UAT.\n\n";
}

echo "🔗 UAT Endpoint: " . $config['base_url'] . $config['base_path'] . "\n";



