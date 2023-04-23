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
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);
        
        if ($type == 'folder') 
        {
            if(FileFolderManager::createFolder($full_path, $folder_name))
                return json_encode(array('code' => 0, 'message' => 'Folder created'));
            return json_encode(array('code' => 1, 'message' => 'Folder is exists'));
        }
        return json_encode(array('code' => 2, 'message' => 'Path is invalid'));
    }

    public static function createFile($path, $file_name, $contents)
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);
        
        if ($type == 'folder') 
        {
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if($ext == null) $file_name .= '.txt';

            if(FileFolderManager::createFile($full_path, $file_name, $contents))
                return json_encode(array('code' => 0, 'message' => 'File created'));
            return json_encode(array('code' => 1, 'message' => 'File is exists'));
        }
        return json_encode(array('code' => 1, 'message' => 'Path is invalid'));
    }
}

?>

