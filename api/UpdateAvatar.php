<?php
require_once '../classes/init.php';
require_once '../classes/auth.php';

// Require authentication
$currentUser = require_auth(null, true);

// Check if file was uploaded
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
            'success' => false,
            'message' => 'No file uploaded or upload error'
    ]);
    exit;
}

// Get file details
$file = $_FILES['avatar'];
$fileName = $file['name'];
$fileTmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = $file['type'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'
    ]);
    exit;
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB in bytes
if ($fileSize > $maxSize) {
    echo json_encode([
            'success' => false,
            'message' => 'File is too large. Maximum size is 5MB.'
    ]);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
$newFileName = 'avatar_' . $currentUser['id'] . '_' . time() . '.' . $fileExtension;
$uploadPath = $uploadDir . $newFileName;

// Move uploaded file
if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
    echo json_encode([
            'success' => false,
            'message' => 'Failed to save the uploaded file.'
    ]);
    exit;
}

// Update user avatar in database
$relativePath = 'uploads/avatars/' . $newFileName;

$DB->store('users')
        ->where('id', '=', $currentUser['id'])
        ->update(['avatar' => $relativePath]);

// Log activity
$DB->store('activity_log')->insert([
        'user_id' => $currentUser['id'],
        'action' => 'profile_update',
        'description' => 'Updated profile avatar',
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'created_at' => time()
]);

// Return success response
echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully',
        'avatar' => $relativePath
]);
