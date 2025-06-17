<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

if ($data !== null) {

    $DB->store('comparisons')
            ->where('id', '=', $data['id'])
            ->update(['status' => 'rejected']);

    echo json_encode(['success' => true]);
}