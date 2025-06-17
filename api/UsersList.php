<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

$clients = $DB->store('users')
        ->fetch();

echo json_encode([$clients]);