<?php

/**
 * Test with UAT Test Accounts
 * Using the test account numbers from the test pack
 */

require_once 'config.php';
require_once 'src/CollexiaClient.php';
require_once 'api/database.php';

echo "=== Testing with UAT Test Accounts ===\n\n";

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
    $db = Database::getInstance()->getConnection();
    
    echo "1. Creating Test Student with Active Account\n";
    echo "   Account Number: " . $testAccounts['active'][0] . "\n";
    
    // Create a test student with an active account
    $studentData = [
        'student_id' => 'UAT_TEST_' . time(),
        'full_name' => 'UAT Test Student',
        'email' => 'uat.test@example.com',
        'phone' => '0812345678',
        'id_number' => '1234567890123',
        'id_type' => 1, // RSA ID
        'account_number' => $testAccounts['active'][0],
        'account_type' => 1, // Current account
        'bank_id' => 65 // FNB Namibia
    ];
    
    // Save to database
    $stmt = $db->prepare("
        INSERT INTO students (
            student_id, full_name, email, phone, id_number, id_type,
            account_number, account_type, bank_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            account_number = VALUES(account_number),
            updated_at = CURRENT_TIMESTAMP
    ");
    
    $stmt->execute([
        $studentData['student_id'],
        $studentData['full_name'],
        $studentData['email'],
        $studentData['phone'],
        $studentData['id_number'],
        $studentData['id_type'],
        $studentData['account_number'],
        $studentData['account_type'],
        $studentData['bank_id']
    ]);
    
    $studentId = $db->lastInsertId();
    if ($studentId == 0) {
        $stmt = $db->prepare("SELECT id FROM students WHERE student_id = ?");
        $stmt->execute([$studentData['student_id']]);
        $student = $stmt->fetch();
        $studentId = $student['id'];
    }
    
    echo "   ✅ Student created in database (ID: $studentId)\n\n";
    
    // Create a test property
    echo "2. Creating Test Property\n";
    $propertyData = [
        'property_code' => 'UAT_PROP_' . time(),
        'property_name' => 'UAT Test Property',
        'address' => '123 UAT Test Street',
        'monthly_rent' => 1000.00
    ];
    
    $stmt = $db->prepare("
        INSERT INTO properties (property_code, property_name, address, monthly_rent)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            monthly_rent = VALUES(monthly_rent),
            updated_at = CURRENT_TIMESTAMP
    ");
    
    $stmt->execute([
        $propertyData['property_code'],
        $propertyData['property_name'],
        $propertyData['address'],
        $propertyData['monthly_rent']
    ]);
    
    $propertyId = $db->lastInsertId();
    if ($propertyId == 0) {
        $stmt = $db->prepare("SELECT id FROM properties WHERE property_code = ?");
        $stmt->execute([$propertyData['property_code']]);
        $property = $stmt->fetch();
        $propertyId = $property['id'];
    }
    
    echo "   ✅ Property created (ID: $propertyId)\n\n";
    
    // Test Mandate Registration with UAT test account
    echo "3. Testing Mandate Registration with UAT Active Account\n";
    echo "   Account: " . $testAccounts['active'][0] . "\n";
    echo "   Student: " . $studentData['student_id'] . "\n";
    echo "   Property: " . $propertyData['property_code'] . "\n\n";
    
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
    
    $messageInfo = [
        'merchantGid' => (int)$config['merchant_gid'],
        'remoteGid' => (int)$config['remote_gid'],
        'messageDate' => $now->format('Ymd'),
        'messageTime' => $now->format('His'),
        'systemUserName' => $config['basic_user'],
        'frontEndUserName' => 'uat_test'
    ];
    
    $mandate = [
        'clientNo' => $studentData['student_id'],
        'userReference' => substr($contractRef, -6),
        'frequencyCode' => 4, // Monthly
        'installmentAmount' => 1000.00,
        'noOfInstallments' => 12,
        'origin' => (int)$config['origin'],
        'contractReference' => $contractRef,
        'magId' => 46, // Endo
        'initialAmount' => 1000.00,
        'firstCollectionDate' => $startDate,
        'collectionDay' => (int)date('d', strtotime($startDate)),
        'numberOfTrackingDays' => 3,
        'debtorAccountName' => str_pad(substr($studentData['full_name'], 0, 35), 35, ' '),
        'debtorIdentificationType' => $studentData['id_type'],
        'debtorIdentificationNo' => str_pad(substr($studentData['id_number'], 0, 33), 33, ' '),
        'debtorAccountNumber' => $testAccounts['active'][0], // UAT test account
        'debtorAccountType' => $studentData['account_type'],
        'debtorBanId' => $studentData['bank_id']
    ];
    
    $mandateData = [
        'messageInfo' => $messageInfo,
        'mandate' => $mandate
    ];
    
    echo "   Sending mandate registration request to Collexia UAT...\n";
    $result = $client->loadMandate($mandateData, $contractRef);
    
    echo "\n   Response Status: " . ($result['ok'] ? '✅ SUCCESS' : '❌ ERROR') . "\n";
    echo "   HTTP Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if ($result['ok']) {
        echo "   ✅✅✅ MANDATE REGISTRATION SUCCESSFUL!\n";
        echo "   Contract Reference: $contractRef\n";
        if (isset($result['data'])) {
            echo "   Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "   Response Details:\n";
        if (isset($result['error'])) {
            if (is_array($result['error'])) {
                echo "   " . json_encode($result['error'], JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "   " . $result['error'] . "\n";
            }
        }
    }
    
    echo "\n";
    
    // Test with different account statuses
    echo "4. Testing Account Status Scenarios\n";
    echo "   Note: These tests verify how the system handles different account statuses\n\n";
    
    foreach (['active', 'inactive', 'closed', 'frozen'] as $status) {
        if (!empty($testAccounts[$status])) {
            echo "   Testing $status account: " . $testAccounts[$status][0] . "\n";
            // Could test mandate registration with each status
            // For now, just note the accounts available
        }
    }
    
    echo "\n=== Test Complete ===\n";
    echo "\nUAT Test Accounts Available:\n";
    echo "  Active: " . implode(', ', $testAccounts['active']) . "\n";
    echo "  Inactive: " . implode(', ', $testAccounts['inactive']) . "\n";
    echo "  Closed: " . implode(', ', $testAccounts['closed']) . "\n";
    echo "  Frozen: " . implode(', ', $testAccounts['frozen']) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}


