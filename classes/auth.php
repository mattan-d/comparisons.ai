<?php
/**
 * Authentication utility functions
 */

/**
 * Check if user is logged in and return user data
 *
 * @param bool $redirect Whether to redirect to login page if not authenticated
 * @return array|false User data if authenticated, false otherwise
 */
function is_logged_in($redirect = false) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isAuthenticated = false;
    $user = null;

    // Check session authentication
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $userId = $_SESSION['user_id'];
        $sessionToken = $_SESSION['session_token'];

        if ($userId && $sessionToken) {
            global $DB;

            // Verify session in database
            $session = $DB->store('user_sessions')
                    ->where('user_id', '=', $userId)
                    ->where('session_token', '=', hash('sha256', $sessionToken))
                    ->where('expires_at', '>', date('Y-m-d H:i:s'))
                    ->one()
                    ->fetch();

            if ($session) {
                // Get user data
                $userData = $DB->store('users')
                        ->where('id', '=', $userId)
                        ->where('status', '=', 'active')
                        ->one()
                        ->fetch();

                if ($userData) {
                    $isAuthenticated = true;
                    $user = [
                            'id' => $userData['id'],
                            'email' => $userData['email'],
                            'name' => $userData['full_name'],
                            'role' => $userData['role'],
                            'avatar' => $userData['avatar'] ?? null,
                            'permissions' => getUserPermissions($userData['role'])
                    ];

                    // Update last activity
                    $DB->store('user_sessions')
                            ->where('id', '=', $session['id'])
                            ->update(['last_activity' => date('Y-m-d H:i:s')]);
                }
            }
        }
    }

    // Check cookie authentication if session is not valid
    if (!$isAuthenticated && isset($_COOKIE['auth_token'])) {
        $cookieToken = $_COOKIE['auth_token'];

        global $DB;

        $session = $DB->store('user_sessions')
                ->where('session_token', '=', hash('sha256', $cookieToken))
                ->where('expires_at', '>', date('Y-m-d H:i:s'))
                ->where('remember_me', '=', 1)
                ->one()
                ->fetch();

        if ($session) {
            $userData = $DB->store('users')
                    ->where('id', '=', $session['user_id'])
                    ->where('status', '=', 'active')
                    ->one()
                    ->fetch();

            if ($userData) {
                // Restore session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['user_name'] = $userData['full_name'];
                $_SESSION['user_role'] = $userData['role'];
                $_SESSION['session_token'] = $cookieToken;
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();

                $isAuthenticated = true;
                $user = [
                        'id' => $userData['id'],
                        'email' => $userData['email'],
                        'name' => $userData['full_name'],
                        'role' => $userData['role'],
                        'avatar' => $userData['avatar'] ?? null,
                        'permissions' => getUserPermissions($userData['role'])
                ];

                // Update last activity
                $DB->store('user_sessions')
                        ->where('id', '=', $session['id'])
                        ->update(['last_activity' => date('Y-m-d H:i:s')]);
            }
        }
    }

    // If not authenticated and redirect is true, redirect to login page
    if (!$isAuthenticated && $redirect) {
        header('Location: /login.php');
        exit;
    }

    return $isAuthenticated ? $user : false;
}

/**
 * Check if user has specific permission
 *
 * @param string $permission Permission to check
 * @param array|null $user User data (optional, will use current user if not provided)
 * @return bool True if user has permission, false otherwise
 */
function has_permission($permission, $user = null) {
    if (!$user) {
        $user = is_logged_in();
        if (!$user) {
            return false;
        }
    }

    return in_array($permission, $user['permissions']);
}

/**
 * Require authentication to access a page or API
 *
 * @param string|array $requiredPermission Optional permission(s) required to access
 * @param bool $jsonResponse Whether to return JSON response (for APIs) or redirect (for pages)
 * @return array|null User data if authenticated, exits otherwise
 */
function require_auth($requiredPermission = null, $jsonResponse = false) {
    $user = is_logged_in();

    if (!$user) {
        if ($jsonResponse) {
            header('Content-Type: application/json');
            echo json_encode([
                    'success' => false,
                    'error' => 'Authentication required',
                    'code' => 401
            ]);
            exit;
        } else {
            header('Location: /login.php');
            exit;
        }
    }

    // Check permission if required
    if ($requiredPermission) {
        $hasPermission = false;

        if (is_array($requiredPermission)) {
            // Check if user has any of the required permissions
            foreach ($requiredPermission as $perm) {
                if (has_permission($perm, $user)) {
                    $hasPermission = true;
                    break;
                }
            }
        } else {
            $hasPermission = has_permission($requiredPermission, $user);
        }

        if (!$hasPermission) {
            if ($jsonResponse) {
                header('Content-Type: application/json');
                echo json_encode([
                        'success' => false,
                        'error' => 'Permission denied',
                        'code' => 403
                ]);
                exit;
            } else {
                // Redirect to dashboard with error
                header('Location: /index.php?error=permission');
                exit;
            }
        }
    }

    return $user;
}

/**
 * Get user permissions based on role
 *
 * @param string $role User role
 * @return array Array of permissions
 */
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

/**
 * Log user activity
 *
 * @param int $userId User ID
 * @param string $action Action performed
 * @param string $details Additional details
 * @param string $ip IP address (optional)
 * @return bool Success status
 */
function log_activity($userId, $action, $details = '', $ip = null) {
    global $DB;

    if (!$ip) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    try {
        $DB->store('user_activity')->insert([
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'created_at' => date('Y-m-d H:i:s')
        ]);
        return true;
    } catch (Exception $e) {
        error_log('Failed to log activity: ' . $e->getMessage());
        return false;
    }
}

?>