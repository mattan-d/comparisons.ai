<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

$clients = $DB->store('clients')
        ->where('user_id', '=', $currentUser['id'])
        ->fetch();

echo json_encode([$clients]);