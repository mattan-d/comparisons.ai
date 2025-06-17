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

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $DB->store('users')
            ->where('id', '=', $data['id'])
            ->update($data);

    echo json_encode(['success' => true]);
}