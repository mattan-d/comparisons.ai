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
  "deals_this_month": "1",
  "deals_change": "+12% מהחודש הקודם",
  "new_clients": "15",
  "clients_change": "+8% מהחודש הקודם",
  "satisfaction_rating": "2.8"
}';