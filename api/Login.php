<?php
require_once '../classes/init.php';

// Set JSON response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$email = trim($data['email']);
$password = trim($data['password']);
$remember = isset($data['remember']) ? (bool) $data['remember'] : false;

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Initialize database connection
    global $DB;

    // Check if user exists and get user data
    $user = $DB->store('users')
            ->where('email', '=', $email)
            ->where('status', '=', 'active')
            ->one()
            ->fetch();

    if (!$user) {
        // User not found
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Invalid password - log failed attempt
        logFailedLoginAttempt($email, $_SERVER['REMOTE_ADDR']);

        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }

    // Check if account is locked due to failed attempts
    if (isAccountLocked($email)) {
        http_response_code(423);
        echo json_encode(['error' => 'Account temporarily locked due to multiple failed login attempts. Please try again later.']);
        exit;
    }

    // Generate session token
    $sessionToken = generateSecureToken();
    $expiresAt = $remember ? date('Y-m-d H:i:s', strtotime('+30 days')) : date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Store session in database
    $sessionId = $DB->store('user_sessions')->insert([
            'user_id' => $user['id'],
            'session_token' => hash('sha256', $sessionToken),
            'expires_at' => $expiresAt,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'remember_me' => $remember ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s')
    ]);

    // Update user's last login
    $DB->store('users')
            ->where('id', '=', $user['id'])
            ->update([
                    'last_login' => date('Y-m-d H:i:s'),
                    'login_count' => ($user['login_count'] ?? 0) + 1
            ]);

    // Clear any failed login attempts
    clearFailedLoginAttempts($email);

    // Start PHP session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['session_token'] = $sessionToken;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Log successful login
    logUserActivity($user['id'], 'login', 'User logged in successfully');

    // Prepare response data
    $responseData = [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar'] ?? null,
                    'permissions' => getUserPermissions($user['role'])
            ],
            'session' => [
                    'token' => $sessionToken,
                    'expires_at' => $expiresAt,
                    'remember_me' => $remember
            ]
    ];

    // Set secure cookie if remember me is checked
    if ($remember) {
        setcookie('auth_token', $sessionToken, [
                'expires' => strtotime('+30 days'),
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
        ]);
    }

    echo json_encode($responseData);

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred. Please try again.']);
}

// Helper functions
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function logFailedLoginAttempt($email, $ipAddress) {
    global $DB;

    $DB->store('failed_login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ipAddress,
            'attempted_at' => date('Y-m-d H:i:s')
    ]);
}

function isAccountLocked($email) {
    global $DB;

    // Check failed attempts in last 15 minutes
    $attempts = $DB->store('failed_login_attempts')
            ->where('email', '=', $email)
            ->where('attempted_at', '>', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->count()
            ->fetch();

    return $attempts >= 5; // Lock after 5 failed attempts
}

function clearFailedLoginAttempts($email) {
    global $DB;

    $DB->store('failed_login_attempts')
            ->where('email', '=', $email)
            ->delete();
}

function getUserPermissions($role) {
    $permissions = [
            'manager' => [
                    'view_dashboard',
                    'manage_users',
                    'manage_clients',
                    'view_reports',
                    'manage_settings',
                    'view_activity',
                    'manage_templates',
                    'export_data'
            ],
            'agent' => [
                    'view_dashboard',
                    'manage_clients',
                    'view_activity',
                    'use_templates'
            ],
            'viewer' => [
                    'view_dashboard',
                    'view_clients'
            ]
    ];

    return $permissions[$role] ?? $permissions['viewer'];
}

function logUserActivity($userId, $action, $description) {
    global $DB;

    $DB->store('user_activity')->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
    ]);
}

?>