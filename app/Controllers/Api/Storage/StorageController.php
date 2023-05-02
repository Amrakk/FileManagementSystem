<?php

namespace App\Controllers\Api\Storage;

use App\Models\Shared\AppConfig;
use App\Models\Storage\FileFolderManager;

class StorageController {

    public static function getFilesFolders($path)
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);
        
        if (array_search($type, ['file', 'folder']) !== false) 
        {
            $FFM = new FileFolderManager($full_path);
            if($type == 'folder') {
                header('Content-Type: application/json');
                $FFM->sortAscPriorFolder();
                return json_encode(array('code' => 0, 'message' => 'Contents found', 'data' => $FFM->getContents()));
            }
            return $FFM->getContents()[0]['data'];
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
        return json_encode(array('code' => 2, 'message' => 'Path is invalid'));
    }

    public static function deleteFileFolder($path) 
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);

        if (array_search($type, ['file', 'folder']) !== false) 
        {
            if(FileFolderManager::deleteFileFolder($full_path))
                return json_encode(array('code' => 0, 'message' => 'File/Folder deleted'));
            return json_encode(array('code' => 1, 'message' => 'File/Folder delete failed'));
        }
        return json_encode(array('code' => 1, 'message' => $type));
    }

    public static function renameFileFolder($path, $new_name) 
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        $type = FileFolderManager::fileOrFolder($full_path);

        if (array_search($type, ['file', 'folder']) !== false) 
        {
            if(FileFolderManager::renameFileFolder($full_path, $new_name))
                return json_encode(array('code' => 0, 'message' => 'File/Folder renamed'));
            return json_encode(array('code' => 1, 'message' => 'File/Folder rename failed'));
        }
        return json_encode(array('code' => 1, 'message' => $type));
    }

    public static function downloadFileFolder($path) 
    {
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);

        $file = FileFolderManager::downloadFileFolder($full_path);
        
        if ($file !== null) {
            $file_path = $file['path'];
            $file_name = $file['name'];
            $file_type = $file['type'];
            
            // Set the headers for the response
            header('Content-Type: ' . $file_type);
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            header('X-Content-Type-Options: nosniff');

            // Output the file content to the response
            return file_get_contents($file_path);

            // Delete the temporary zip file
            if($file['type'] == 'application/zip')
                unlink($file_path);
            
        } else {
            return json_encode(array('code' => 1, 'message' => 'Invalid file or folder'));
        }
        
    }


    public static function uploadFiles($path, $files) 
    {
        $allow_extension = AppConfig::getExt();
        $full_path = AppConfig::get('STORAGE_PATH') . DIRECTORY_SEPARATOR . $path;
        $full_path = str_replace('/', DIRECTORY_SEPARATOR , $full_path);
        
        $type = FileFolderManager::fileOrFolder($full_path);
        if($type == 'folder') {
            $FFM = new FileFolderManager($full_path);
            $uploaded_files = $FFM->uploadFiles($files, $allow_extension); 

            return json_encode(array('code' => 0, 'message' => 'Files uploaded', 'data' => $uploaded_files));
        }
        return json_encode(array('code' => 1, 'message' => 'Path is invalid'));
    }
}

?>