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


    $data['updated_at'] = time();

    $DB->store('users')
            ->where('id', '=', $currentUser['id'])
            ->update($data);

    echo json_encode(['success' => true]);
}