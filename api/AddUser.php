<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

if ($data !== null) {

    $data['created_at'] = time();
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    $DB->store('users')
            ->insert($data);

    echo json_encode(['success' => true]);
}