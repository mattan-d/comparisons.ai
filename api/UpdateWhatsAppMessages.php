<?php

require_once __DIR__ . '/../classes/init.php';
require_once __DIR__ . '/../classes/auth.php';

// Require authentication for this page
$currentUser = require_auth();

// Check if user is authenticated
if (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Extract token
$token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);

// Validate token (simplified for example)
if (!$token || $token !== $_SESSION['authToken']) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

// Get user ID from session
$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Check if request method is POST
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
if (!isset($data['outbound_message']) || !isset($data['return_message']) || !isset($data['followup_message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $db = new MySQLDB();

    // Check if user has existing WhatsApp messages
    $query = "SELECT * FROM whatsapp_messages WHERE user_id = ?";
    $existingMessages = $db->query($query, [$userId])->fetchAll();

    if (count($existingMessages) > 0) {
        // Update existing messages
        $query = "UPDATE whatsapp_messages 
                  SET outbound_message = ?, return_message = ?, followup_message = ?, updated_at = NOW() 
                  WHERE user_id = ?";
        $db->query($query, [
                $data['outbound_message'],
                $data['return_message'],
                $data['followup_message'],
                $userId
        ]);
    } else {
        // Insert new messages
        $query = "INSERT INTO whatsapp_messages (user_id, outbound_message, return_message, followup_message, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())";
        $db->query($query, [
                $userId,
                $data['outbound_message'],
                $data['return_message'],
                $data['followup_message']
        ]);
    }

    // Return success response
    echo json_encode([
            'success' => true,
            'message' => 'WhatsApp messages updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    exit;
}
