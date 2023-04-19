<?php
    require_once '../vendor/autoload.php';
   
    use App\Models\Shared\AppConfig;
    use App\Controllers\Api\Auth\LoginController;
    $app_config = new AppConfig();

    $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
    $main = explode('/', $request_uri[0]);
    $category = $main[2] ?? '';
    $action = $main[3] ?? '';
    $param = $request_uri[1] ?? '';

    if($category == 'auth') {
        if($action == 'login') {
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
            echo LoginController::verify($data->username, $data->password);
            exit;
        }
    }
    
    
    


?>