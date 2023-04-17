<?php
    require_once '../vendor/autoload.php';
   
    use App\Models\Shared\AppConfig;
    use App\Controllers\Api\Auth\SignupController;
    use App\Controllers\Api\Auth\LoginController;
    use App\Controllers\Api\Auth\VerifyController;
    use App\Models\Shared\Database;

    $app_config = new AppConfig();

    // $uri = $_SERVER['REQUEST_URI'];
    print_r($_SERVER['REQUEST_URI']);

    new Database();


?>