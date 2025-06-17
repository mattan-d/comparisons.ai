<?php
require_once '../classes/init.php';
require_once '../classes/auth.php';

// Require authentication
$currentUser = require_auth(null, true);

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

if ($data !== null) {

    $data['user_id'] = $currentUser['id'];
    $data['updated_at'] = time();

    $DB->store('clients')
            ->where('id_number', '=', $data['id_number'])
            ->update($data);

    echo json_encode(['success' => true]);
}