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

    $DB->store('comparisons')
            ->where('id', '=', $data['id'])
            ->update($data);

    $DB->store('activity_log')
            ->insert([
                    'user_id' => $currentUser['id'],
                    'action' => 'עודכן',
                    'entity_type' => 'comparison',
                    'entity_id' => $data['id'],
                    'description' => 'הסטטוס שונה ל' . $data['status'] . ', סיבה: ' . $data['notes'],
                    'created_at' => time()
            ]);

    echo json_encode(['success' => true]);
}