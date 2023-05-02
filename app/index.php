<?php
    require_once '../vendor/autoload.php';
   
    use App\Models\Shared\AppConfig;
    use App\Controllers\Api\HomeController;
    use App\Controllers\Api\Auth\LoginController;
    use App\Controllers\Api\Auth\RegisterController;
    use App\Controllers\Api\Storage\StorageController;

    $app_config = new AppConfig();

    $method = $_SERVER['REQUEST_METHOD'];

    // handle request uri
    $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
    $main = explode('/', $request_uri[0]);
    $category = $main[2] ?? '';
    $action = $main[3] ?? '';

    // handle request params
    $params = urldecode($request_uri[1] ?? '');
    $params = explode('&', $params);
    $params = array_filter($params);
    $params = array_map(function($param) {
        $param = explode('=', $param);
        return [$param[0] => $param[1]];
    }, $params);
    

    // handle request body
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);

    if($category == 'auth') 
    {
        if($action == 'login') echo LoginController::verify($data->username, $data->password);
        else if($action == 'register') 
            echo RegisterController::register($data->first_name, $data->last_name, $data->email, $data->username, $data->password);

        else if($action == 'activate') echo RegisterController::activateAccount($data->email,$data->token);
        else if($action == 'forgot') echo LoginController::forgot($data->email);
        else if($action == 'reset') echo LoginController::resetPassword($data->email, $data->token, $data->password);
        else echo json_encode(array('code' => 20, 'message' => 'Invalid action or request method is not supported'));
    } 
    else if($category == 'user') 
    {
        if($action == 'get_profile') echo HomeController::getProfile($params[0]['id'] ?? '');
    }
    else if($category == 'storage') 
    {
        if($action == 'get_files_folders') print_r(StorageController::getFilesFolders($params[0]['path'] ?? ''));
        else if($action == 'create_folder') echo StorageController::createFolder($data->folder_path, $data->folder_name);
        else if($action == 'create_file') echo StorageController::createFile($_POST['current-path'] ?? '', $_POST['file-name'] ?? '', $_POST['file-contents'] ?? '');
        else if($action == 'delete_file_folder') echo StorageController::deleteFileFolder($data->path);
        else if($action == 'rename_file_folder') echo StorageController::renameFileFolder($data->path, $data->new_name);
        else if($action == 'download_file_folder') echo StorageController::downloadFileFolder($data->path);
        else if($action == 'upload_file_folder') echo StorageController::uploadFiles($_POST['path'], $_FILES['files']);

        else echo json_encode(array('code' => 20, 'message' => 'Invalid action or request method is not supported'));
    }
    else echo json_encode(array('code' => 20, 'message' => 'Invalid action or request method is not supported'));
    
    exit();
?>