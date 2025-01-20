<?php
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$storage = new FileStorage($config['json_file']);

// Get and delete expired files
$currentTime = time();
$expiredFiles = $storage->deleteExpiredFiles($currentTime);

foreach ($expiredFiles as $file) {
    $filepath = $config['upload_dir'] . $file['filepath'];
    
    // Delete physical file
    if (file_exists($filepath)) {
        unlink($filepath);
        
        // Get directory path
        $dir = dirname($filepath);
        
        // Remove empty directory if it exists and is empty
        if (is_dir($dir) && count(scandir($dir)) === 2) { // 2 because of . and ..
            rmdir($dir);
        }
    }
}
