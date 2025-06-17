<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

$comparisons = $DB->store('comparisons')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();

$clients = $DB->store('clients')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();

$response = [];
$response['total_clients'] = count($clients);
$response['clients_change'] = '+' . count($clients) . ' החודש'; // count($clients);
$response['clients_change_value'] = 0;
$response['comparisons_this_month'] = count($comparisons);
$response['comparisons_change'] = '+0 מהשבוע הקודם';
$response['comparisons_change_value'] = 0;
$response['deals_closed'] = 0;
$response['deals_change'] = '+0 מהשבוע הקודם';
$response['deals_change_value'] = 0;
$response['total_savings'] = 0;
$response['savings_change'] = 'החודש';
$response['savings_change_value'] = 0;

echo json_encode($response);