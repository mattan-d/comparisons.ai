<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

$comparisons = $DB->store('comparisons')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();

$response = [];
foreach ($comparisons as $comparison) {
    $client = $DB->store('clients')
            ->where('id', '=', $comparison['client_id'])
            ->one()
            ->fetch();

    $comparison['client_name'] = $client['name'];
    $comparison['insurance_type'] = 'ביטוח נסיעות';
    $response[] = $comparison;
}

echo json_encode([$response]);