<?php
// upload.php
header('Content-Type: application/json');

try {
    $config = require __DIR__ . '/config.php';
    require_once __DIR__ . '/helpers.php';

    // Validate file existence
    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file size
    if ($file['size'] > $config['max_file_size']) {
        throw new Exception('File too large');
    }

    // Validate file extension
    if (!in_array($extension, $config['allowed_extensions'])) {
        throw new Exception('File type not allowed');
    }

    // Generate random path for file storage
    function generateRandomPath($length = 16) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $randomString;
    }

    // Create upload directory
    $random_dir = generateRandomPath();
    $upload_subdir = $config['upload_dir'] . $random_dir . '/';

    if (!is_dir($upload_subdir)) {
        if (!mkdir($upload_subdir, 0750, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename and paths
    $unique_filename = uniqid() . '.' . $extension;
    $relative_path = $random_dir . '/' . $unique_filename;
    $upload_path = $config['upload_dir'] . $relative_path;

    // Initialize storage
    $storage = new FileStorage($config['json_file']);

    // Set times
    $current_time = time();
    $expiry_time = $current_time + ($config['default_expiry'] * 3600);

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Save file information to JSON storage
    $fileInfo = [
        'filename' => $unique_filename,
        'filepath' => $relative_path,
        'original_name' => $file['name'],
        'upload_time' => $current_time,
        'expiry_time' => $expiry_time
    ];
    
    $storage->addFile($fileInfo);
    
    // Generate and return download URL
    $download_url = $config['domain'] . '/dl.php?file=' . $unique_filename;
    echo json_encode([
        'success' => true,
        'filename' => $file['name'],
        'url' => $download_url
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
