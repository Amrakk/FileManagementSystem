<?php

namespace App\Controllers\Api\Storage;

use App\Models\Shared\AppConfig;
use App\Models\Storage\FileFolderManager;

class StorageController {

    public static function getFilesFolders($path)
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR .$path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);
        
        if (array_search($type, ['file', 'folder']) !== false) 
        {
            $FFM = new FileFolderManager($full_path);
            if($type == 'folder') $FFM->sortAscPriorFolder();
            return json_encode(array('code' => 0, 'message' => 'Contents found', 'data' => $FFM->getContents()));
        }
        return json_encode(array('code' => 1, 'message' => $type));
    }


    public static function createFolder($path, $folder_name)
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR .$path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);
        
        if ($type == 'folder') 
        {
            if(FileFolderManager::createFolder($full_path, $folder_name))
                return json_encode(array('code' => 0, 'message' => 'Folder created'));
        }
        return json_encode(array('code' => 1, 'message' => 'Not a folder'));
    }
}

?>

