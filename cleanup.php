<?php
$config = require __DIR__ . '/config.php';
$db = new SQLite3($config['db_file']);

// Get expired files
$stmt = $db->prepare('SELECT filepath FROM files WHERE expiry_time < :current_time');
$stmt->bindValue(':current_time', time());
$result = $stmt->execute();

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $filepath = $config['upload_dir'] . $row['filepath'];
    
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

// Delete database records
$db->exec('DELETE FROM files WHERE expiry_time < ' . time());
$db->close();