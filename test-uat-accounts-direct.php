<?php

/**
 * Test Collexia API with UAT Test Accounts
 * Using the actual test account numbers from the test pack
 */

require_once 'config.php';
require_once 'src/CollexiaClient.php';

echo "=== Testing Collexia API with UAT Test Accounts ===\n\n";

// UAT Test Accounts from test pack
$testAccounts = [
    'active' => [
        '62001543455',
        '62001543405',
        '62001543398'
    ],
    'inactive' => [
        '62001623380',
        '62002288539'
    ],
    'closed' => [
        '62001871799',
        '62003081560'
    ],
    'frozen' => [
        '62001914812'
    ]
];

try {
    $config = require 'config.php';
    $client = new CollexiaClient($config);
    
    echo "UAT Test Accounts Available:\n";
    echo "  ✅ Active: " . implode(', ', $testAccounts['active']) . "\n";
    echo "  ⚠️  Inactive: " . implode(', ', $testAccounts['inactive']) . "\n";
    echo "  ❌ Closed: " . implode(', ', $testAccounts['closed']) . "\n";
    echo "  🔒 Frozen: " . implode(', ', $testAccounts['frozen']) . "\n\n";
    
    // Test 1: Try to register a mandate with an ACTIVE account
    echo "1. Testing Mandate Registration with ACTIVE Account\n";
    echo "   Account: " . $testAccounts['active'][0] . "\n";
    
    // Generate contract reference
    require_once 'api/utils/ContractReference.php';
    $contractRef = ContractReference::generate(
        $config['merchant_gid'],
        date('Ymd')
    );
    
    echo "   Contract Reference: $contractRef\n";
    
    // Prepare mandate data
    $now = new DateTime();
    $startDate = date('Ymd', strtotime('+1 month'));
    $collectionDay = (int)date('d', strtotime($startDate));
    
    // For monthly, if day > 28, use 31 (last day of month)
    if ($collectionDay > 28) {
        $collectionDay = 31;
    }
    
    $messageInfo = [
        'merchantGid' => (int)$config['merchant_gid'],
        'remoteGid' => (int)$config['remote_gid'],
        'messageDate' => $now->format('Ymd'),
        'messageTime' => $now->format('His'),
        'systemUserName' => $config['basic_user'],
        'frontEndUserName' => 'uat_test'
    ];
    
    $mandate = [
        'clientNo' => 'UATTEST' . substr(time(), -6), // Alphanumeric, max 15 chars
        'userReference' => substr($contractRef, -6),
        'frequencyCode' => 4, // Monthly
        'installmentAmount' => 100.00, // Smaller amount for UAT testing
        'noOfInstallments' => 12,
        'origin' => (int)$config['origin'],
        'contractReference' => $contractRef,
        'magId' => 46, // Endo
        'initialAmount' => 100.00, // Smaller amount for UAT testing
        'firstCollectionDate' => $startDate,
        'collectionDay' => $collectionDay,
        'numberOfTrackingDays' => 3,
        'debtorAccountName' => str_pad('UAT TEST STUDENT', 35, ' '),
        'debtorIdentificationType' => 1, // RSA ID
        'debtorIdentificationNo' => str_pad('1234567890123', 33, ' '),
        'debtorAccountNumber' => $testAccounts['active'][0], // UAT ACTIVE test account
        'debtorAccountType' => 1, // Current account
        'debtorBanId' => 65 // FNB Namibia
    ];
    
    $mandateData = [
        'messageInfo' => $messageInfo,
        'mandate' => $mandate
    ];
    
    echo "   Sending request to Collexia UAT...\n";
    $result = $client->loadMandate($mandateData, $contractRef);
    
    echo "\n   HTTP Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if ($result['ok']) {
        echo "   ✅✅✅ SUCCESS! Mandate registered successfully!\n";
        echo "   Contract Reference: $contractRef\n";
        if (isset($result['data'])) {
            echo "   Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
        
        // Now test checking the status
        echo "\n2. Testing Mandate Status Check\n";
        echo "   Contract Reference: $contractRef\n";
        
        $statusPayload = [
            'contractReference' => $contractRef,
            'merchantGid' => (int)$config['merchant_gid'],
            'frontEndUserName' => 'uat_test',
            'remoteGid' => (int)$config['remote_gid']
        ];
        
        echo "   Checking mandate status...\n";
        $statusResult = $client->mandateFinalFate($statusPayload);
        
        echo "   HTTP Status: " . ($statusResult['status'] ?? 'N/A') . "\n";
        
        if ($statusResult['ok']) {
            echo "   ✅ Status check successful!\n";
            if (isset($statusResult['data'])) {
                echo "   Response: " . json_encode($statusResult['data'], JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "   Response: " . json_encode($statusResult['error'] ?? 'Unknown error', JSON_PRETTY_PRINT) . "\n";
        }
        
    } else {
        echo "   Response:\n";
        if (isset($result['error'])) {
            if (is_array($result['error'])) {
                if (isset($result['error']['errors'])) {
                    foreach ($result['error']['errors'] as $error) {
                        echo "   - Code: " . ($error['code'] ?? 'N/A') . "\n";
                        echo "   - Message: " . ($error['message'] ?? 'N/A') . "\n";
                        echo "   - Detail: " . ($error['detail'] ?? 'N/A') . "\n";
                    }
                } else {
                    echo "   " . json_encode($result['error'], JSON_PRETTY_PRINT) . "\n";
                }
            } else {
                echo "   " . $result['error'] . "\n";
            }
        }
    }
    
    echo "\n";
    
    // Test 2: Test with CLOSED account (should fail)
    echo "3. Testing Mandate Registration with CLOSED Account (Expected to Fail)\n";
    echo "   Account: " . $testAccounts['closed'][0] . "\n";
    
    $contractRef2 = ContractReference::generate($config['merchant_gid'], date('Ymd'));
    $mandate2 = $mandate;
    $mandate2['contractReference'] = $contractRef2;
    $mandate2['debtorAccountNumber'] = $testAccounts['closed'][0]; // CLOSED account
    $mandate2['clientNo'] = 'UATCLOSED' . substr(time(), -6); // Alphanumeric, max 15 chars
    
    $mandateData2 = [
        'messageInfo' => $messageInfo,
        'mandate' => $mandate2
    ];
    
    echo "   Sending request...\n";
    $result2 = $client->loadMandate($mandateData2, $contractRef2);
    
    echo "   HTTP Status: " . ($result2['status'] ?? 'N/A') . "\n";
    
    if ($result2['status'] == 400 || $result2['status'] == 422) {
        echo "   ✅ Expected error received (account is closed)\n";
        if (isset($result2['error']['errors'])) {
            foreach ($result2['error']['errors'] as $error) {
                echo "   - " . ($error['message'] ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "   Response: " . json_encode($result2['error'] ?? 'Unknown', JSON_PRETTY_PRINT) . "\n";
    }
    
    echo "\n=== Test Summary ===\n";
    echo "✅ Tested with UAT Active Account: " . $testAccounts['active'][0] . "\n";
    echo "✅ Tested with UAT Closed Account: " . $testAccounts['closed'][0] . "\n";
    echo "\nNote: These are the actual UAT test accounts from the test pack.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

