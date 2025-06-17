<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

$statuses = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'completed' => 0];
$comparisons = $DB->store('comparisons')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();
foreach ($comparisons as $comparison) {
    $statuses['total']++;
    $statuses[$comparison['status']]++;
}

echo json_encode($statuses);