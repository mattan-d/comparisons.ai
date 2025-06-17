<?php

include __DIR__ . '/../classes/init.php';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Decode if it's JSON
$data = json_decode($payload, true);

/*
$user = $DB->store('users')
        ->where('id', $data['id'])
        ->fetch();*/

echo '{
  "outbound_message": "שלום! אני בדרך אליך...",
  "return_message": "תודה על הפגישה...",
  "followup_message": "שלום! רציתי לבדוק..."
}';