<?php
return [
    'domain' => 'https://domain.com',
    'max_file_size' => 256 * 1024 * 1024, // 256MB in bytes
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'tar', 'mkv', 'mp4', 'mp3', 'ipa', 'tipa', 'deb'],
    'expiry_hours' => [3, 6, 12, 24],
    'upload_dir' => __DIR__ . '/uploads/',
    'default_expiry' => 3, // Default expiry time in hours
    'db_file' => __DIR__ . '/lt_' . hash('sha256', 'your_secret_key_here') . '.db' // The database file name is hashed
];