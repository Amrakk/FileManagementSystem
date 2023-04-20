<?php
    require_once '../vendor/autoload.php';
   
    use App\Models\Shared\AppConfig;
    use App\Controllers\Api\HomeController;
    use App\Controllers\Api\Auth\LoginController;
    use App\Controllers\Api\Auth\RegisterController;

    $app_config = new AppConfig();

    $method = $_SERVER['REQUEST_METHOD'];

    // handle request uri
    $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
    $main = explode('/', $request_uri[0]);
    $category = $main[2] ?? '';
    $action = $main[3] ?? '';

    // handle request params
    $params = $request_uri[1] ?? '';
    $params = explode('&', $params);
    $params = array_filter($params);
    $params = array_map(function($param) {
        $param = explode('=', $param);
        return [$param[0] => $param[1]];
    }, $params);
    
    // params template : [ ['id' => 1], ['name' => 'abc'] ] 2d array

    // handle request body
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);
    

    if($category == 'auth') 
    {
        if($action == 'login') echo LoginController::verify($data->username, $data->password);
        else if($action == 'register') echo RegisterController::register($data->first_name, $data->last_name, 
                                                                         $data->email, $data->username, $data->password);
        // else if($action == 'forgot') echo LoginController::forgot($data->email);
        
    } 
    else if($category == 'user') 
    {
        if($action == 'profile') echo HomeController::getProfile($params[0]['id']);
    }
    // else echo json_encode(array('code' => 20, 'message' => 'Invalid action or request method is not supported'));
    


    exit();
    
?>