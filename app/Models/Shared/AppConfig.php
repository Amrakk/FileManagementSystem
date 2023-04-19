<?php
namespace App\Models\Shared;

    class AppConfig {

        private static $app_config;

        public static function get($key, $default = null)
        {
            if (is_null(self::$app_config)) {
                self::$app_config = require_once(__DIR__.'/../../../config.php');
            }

            return !empty(self::$app_config[$key])?self::$app_config[$key]:$default;
        }
    }
?>