<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

$clients = $DB->store('clients')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();

foreach ($clients as $key => $client) {
    $comparisons = $DB->store('comparisons')
            ->where('client_id', '=', $client['id'])
            ->fetch();

    $policies = $DB->store('comparisons')
            ->where('client_id', '=', $client['id'])
            ->where('status', '=', 'approved')
            ->fetch();

    $clients[$key]['total_comparisons'] = count($comparisons);
    $clients[$key]['total_policies'] = count($policies);
}

echo json_encode([$clients]);
