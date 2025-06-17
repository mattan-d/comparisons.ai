<?php
require_once '../classes/init.php';

// Set JSON response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;
    $sessionToken = $_SESSION['session_token'] ?? null;

    if ($userId && $sessionToken) {
        global $DB;

        // Remove session from database
        $DB->store('user_sessions')
                ->where('user_id', '=', $userId)
                ->where('session_token', '=', hash('sha256', $sessionToken))
                ->delete();

        // Log logout activity
        logUserActivity($userId, 'logout', 'User logged out');
    }

    // Clear PHP session
    session_unset();
    session_destroy();

    // Clear auth cookie
    if (isset($_COOKIE['auth_token'])) {
        setcookie('auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
        ]);
    }

    echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
    ]);

} catch (Exception $e) {
    error_log('Logout error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred']);
}

function logUserActivity($userId, $action, $description) {
    global $DB;

    try {
        $DB->store('user_activity')->insert([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        error_log('Failed to log user activity: ' . $e->getMessage());
    }
}
?>
