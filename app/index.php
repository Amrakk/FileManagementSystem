<?php
    require_once '../vendor/autoload.php';
   
    use App\Models\Shared\AppConfig;
    use App\Controllers\Api\Auth\LoginController;
    use App\Controllers\Api\Auth\RegisterController;

    $app_config = new AppConfig();

    $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
    $main = explode('/', $request_uri[0]);
    $category = $main[2] ?? '';
    $action = $main[3] ?? '';
    $param = $request_uri[1] ?? '';

    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    if($category == 'auth') {
        if($action == 'login') {
            echo LoginController::verify($data->username, $data->password);
        } else if($action == 'register') {
            echo RegisterController::register($data->first_name, $data->last_name, $data->email, $data->username, $data->password);
                 
        }
    }

    exit();
    
?>