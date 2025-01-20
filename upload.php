<?php
header('Content-Type: application/json');

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

// Validate file
if (!isset($_FILES['file'])) {
    die(json_encode(['success' => false, 'error' => 'No file uploaded']));
}

$file = $_FILES['file'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Check file size
if ($file['size'] > $config['max_file_size']) {
    die(json_encode(['success' => false, 'error' => 'File too large']));
}

// Check file extension
if (!in_array($extension, $config['allowed_extensions'])) {
    die(json_encode(['success' => false, 'error' => 'File type not allowed']));
}

// Generate random subdirectory path
function generateRandomPath() {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $length = 16;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $randomString;
}

// Create random subdirectory
$random_dir = generateRandomPath();
$upload_subdir = $config['upload_dir'] . $random_dir . '/';

// Create directory if it doesn't exist
if (!is_dir($upload_subdir)) {
    mkdir($upload_subdir, 0750, true);
}

// Generate unique filename
$unique_filename = uniqid() . '.' . $extension;
$relative_path = $random_dir . '/' . $unique_filename;
$upload_path = $config['upload_dir'] . $relative_path;

$storage = new FileStorage($config['json_file']);

$current_time = time();
$expiry_time = $current_time + ($config['default_expiry'] * 3600);

// Move uploaded file and generate download URL
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Save file information
    $storage->addFile([
        'filename' => $unique_filename,
        'filepath' => $relative_path,
        'original_name' => $file['name'],
        'upload_time' => $current_time,
        'expiry_time' => $expiry_time
    ]);
    
    $download_url = $config['domain'] . '/dl.php?file=' . $unique_filename;
    echo json_encode([
        'success' => true,
        'filename' => $file['name'],
        'url' => $download_url
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
}
