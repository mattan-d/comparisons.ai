<?php

include __DIR__ . '/../classes/init.php';
include __DIR__ . '/../classes/auth.php';

// Set JSON response header
header('Content-Type: application/json');

// Require authentication for this API
$currentUser = require_auth(null, true);

// Get user profile data
$user = $DB->store('users')
        ->where('id', '=', $currentUser['id'])
        ->one()
        ->fetch();

if (!$user) {
    echo json_encode([
            'success' => false,
            'error' => 'User not found',
            'code' => 404
    ]);
    exit;
}

// Log this activity
log_activity($currentUser['id'], 'view_profile', 'User viewed their profile');

// Return user profile data
echo json_encode([
        'success' => true,
        'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'username' => $user['username'] ?? 'Unknown',
                'role' => $user['role'],
                'avatar' => $user['avatar'] ?? null,
                'phone' => $user['phone'] ?? null,
                'address' => $user['address'] ?? null,
                'bio' => $user['bio'] ?? null,
                'created_at' => $user['created_at'],
                'last_login' => $user['last_login']
        ]
]);
