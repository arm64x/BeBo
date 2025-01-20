<?php
// upload.php
header('Content-Type: application/json');

try {
    $config = require __DIR__ . '/config.php';
    require_once __DIR__ . '/helpers.php';

    // Validate file
    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Check file size
    if ($file['size'] > $config['max_file_size']) {
        throw new Exception('File too large');
    }

    // Check file extension
    if (!in_array($extension, $config['allowed_extensions'])) {
        throw new Exception('File type not allowed');
    }

    // Generate random path
    function generateRandomPath($length = 16) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $randomString;
    }

    // Setup directories
    $random_dir = generateRandomPath();
    $upload_subdir = $config['upload_dir'] . $random_dir . '/';

    if (!is_dir($upload_subdir)) {
        if (!mkdir($upload_subdir, 0750, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Setup filenames
    $unique_filename = uniqid() . '.' . $extension;
    $relative_path = $random_dir . '/' . $unique_filename;
    $upload_path = $config['upload_dir'] . $relative_path;

    // Initialize storage
    $storage = new FileStorage($config['json_file']);
    
    $current_time = time();
    $expiry_time = $current_time + ($config['default_expiry'] * 3600);

    // Move and save file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Save to JSON
    $fileInfo = [
        'filename' => $unique_filename,
        'filepath' => $relative_path,
        'original_name' => $file['name'],
        'upload_time' => $current_time,
        'expiry_time' => $expiry_time
    ];
    
    if (!$storage->addFile($fileInfo)) {
        throw new Exception('Failed to save file information');
    }

    // Return success response
    exit(json_encode([
        'success' => true,
        'filename' => $file['name'],
        'url' => $config['domain'] . '/dl.php?file=' . $unique_filename
    ]));

} catch (Exception $e) {
    exit(json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]));
}
