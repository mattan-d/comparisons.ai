<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

$activity_logs = $DB->store('activity_log')
        ->where('entity_id', '=', $_GET['id'])
        ->fetch();

$response = [];
foreach ($activity_logs as $key => $activity_log) {
    $user = $DB->store('users')
            ->where('id', '=', $activity_log['user_id'])
            ->one()
            ->fetch();

    $activity_log['user_name'] = $user['name'];
    $response[] = $activity_log;
}

echo json_encode($response);

/*
 * [
  {
    "action": "עודכן",
    "description": "הסטטוס שונה לאושר",
    "created_at": "1703515200",
    "user_name": "אבי כהן"
  }
]
 * */