<?php
namespace App\Models\Shared;

    class AppConfig {
        function __construct() {
            require_once __DIR__ .'/../../../config.php';
            foreach($global_path as $key => $value) {
                define($key, $value);
                
            }
        }
    }

    
    
?>