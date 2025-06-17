<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

if ($data !== null) {

    $DB->store('clients')
            ->where('user_id', '=', $data['user_id'])
            ->delete();

    $data['user_id'] = $currentUser['id'];
    $data['created_at'] = time();

    $DB->store('clients')
            ->insert($data);

    echo json_encode(['success' => true]);
}