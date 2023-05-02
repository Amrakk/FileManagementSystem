<?php

    return [
        'DB_HOST' => 'localhost',
        'DB_PORT' => '3306',
        'DB_DATABASE' => 'cloudstorage',
        'DB_USERNAME' => 'root',
        'DB_PASSWORD' => '',

        'ROOT_PATH' => __DIR__,
        'APP_PATH' => __DIR__ . DIRECTORY_SEPARATOR . 'app',
        'PUBLIC_PATH' => __DIR__ . DIRECTORY_SEPARATOR .'public',
        'STORAGE_PATH' => __DIR__ . DIRECTORY_SEPARATOR . 'storage',

        'ALLOWED_FILE_EXTENSIONS' => [
            'image' => ['jpg', 'jpeg', 'png'],
            'video' => ['mp4', 'avi', 'mkv'],
            'audio' => ['mp3', 'wav'],
            'document' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt'],
            'archive' => ['zip', 'rar'],
            'code' => ['html', 'css', 'js', 'php', 'py', 'java', 'c', 'cpp', 'cs', 'vb', 'sql', 'xml', 'json'],
            'other' => ['exe', 'msi', 'apk', 'iso', 'img', 'torrent']
        ]
    ];
    
    
?>
