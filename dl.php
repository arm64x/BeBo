<?php
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

if (!isset($_GET['file'])) {
    die('No file specified');
}

$filename = $_GET['file'];
$storage = new FileStorage($config['json_file']);

// Get file information and verify it exists
$fileInfo = $storage->getFileByName($filename);

if (!$fileInfo) {
    die('File not found');
}

// Check if file has expired
if (time() > $fileInfo['expiry_time']) {
    die('File has expired');
}

$filepath = $config['upload_dir'] . $fileInfo['filepath'];
if (!file_exists($filepath)) {
    die('File not found');
}

// Force download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileInfo['original_name'] . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache');

readfile($filepath);
