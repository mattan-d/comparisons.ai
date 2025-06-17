<?php
require_once '../classes/init.php';
require_once '../classes/auth.php';

// Set JSON response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

try {
    $user = is_logged_in();

    echo json_encode([
            'authenticated' => ($user !== false),
            'user' => $user
    ]);

} catch (Exception $e) {
    error_log('Auth check error: ' . $e->getMessage());
    echo json_encode([
            'authenticated' => false,
            'user' => null,
            'error' => 'Server error occurred'
    ]);
}
?>
