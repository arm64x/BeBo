<?php
$config = require 'config.php';

if (!isset($_GET['file'])) {
    die('No file specified');
}

$filename = $_GET['file'];
$db = new SQLite3($config['db_file']);

// Get file information and verify it exists
$stmt = $db->prepare('SELECT * FROM files WHERE filename = :filename');
$stmt->bindValue(':filename', $filename);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
    die('File not found');
}

// Check if file has expired
if (time() > $result['expiry_time']) {
    die('File has expired');
}

$filepath = $config['upload_dir'] . $result['filepath'];
if (!file_exists($filepath)) {
    die('File not found');
}

// Force download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $result['original_name'] . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache');

readfile($filepath);
$db->close();