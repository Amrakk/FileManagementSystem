<?php
    namespace App\Models\Storage;

    use App\Models\Storage\File;
    use App\Models\Storage\Folder;

    class FileFolderManager {
        
        private $current_path;
        private $contents;

        public function __construct($path) {
            $this->current_path = $path;
            if (is_dir($path)) $this->contents = $this->getFolderContents($path);
            else $this->contents = $this->getFileContents($path);
        }

        private function getFileContents($path)
        {
            return [
                [
                    'type' => 'file',
                    'info' => (new File($path))->getInfo(),
                    'data' => (new File($path))->getData()
                ]
            ];
        }

        private function getFolderContents($path)
        {
            $contents = [];
            $files = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');

            foreach ($files as $file) {
                $contents[] = [
                    'type' => is_file($file) ? 'file' : 'folder',
                    'info' => is_file($file) ? (new File($file))->getInfo() : (new Folder($file))->getInfo()
                ];
            }
            return $contents;
        }

        public function getTotalSize()
        {
            $total_size = 0;
            foreach ($this->contents as $file_folder) {
                $total_size += $file_folder['info']['size'];
            }
            return $total_size;
        }

        public function getFileList()
        {
            return $this->getFileOrFolder('file');
        }

        public function getFolderList()
        {
            return $this->getFileOrFolder('folder');
        }

        public function getFileOrFolder($type) {
            $filtered_files_folders = array_filter($this->contents, function ($file_folder) use ($type) {
                return $file_folder['type'] === $type;
            });
            return array_values($filtered_files_folders);
        }

        public function sortAscPriorFolder()
        {
            usort($this->contents, function ($a, $b) {
                if ($a['type'] == 'folder' && $b['type'] == 'file') {
                    return -1;
                }
                if ($a['type'] == 'file' && $b['type'] == 'folder') {
                    return 1;
                }
                return strcasecmp($a['info']['name'], $b['info']['name']);
            });
        }

        // Static methods

        public static function createFolder($path, $folder_name)
        {
            $folder_path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder_name;
            if (!file_exists($folder_path)) {
                return mkdir($folder_path);
            }
            return false;
        }

        public static function createFile($path, $file_name, $content)
        {
            $file_path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file_name;
            if (!file_exists($file_path)) {
                $file = fopen($file_path, 'w');
                fwrite($file, $content);
                fclose($file);
                return true;
            }
            return false;
        }

        public static function deleteFileFolder($path)
        {
            if (is_dir($path)) {
                $files = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
                foreach ($files as $file) { self::deleteFileFolder($file); }
                return rmdir($path);
            } 
            return unlink($path);
        }

        public static function renameFileFolder($path, $new_name)
        {
            if (is_file($path)) 
                $new_path = dirname($path) . DIRECTORY_SEPARATOR . $new_name . '.' . pathinfo($path, PATHINFO_EXTENSION);
            else 
                $new_path = dirname($path) . DIRECTORY_SEPARATOR . $new_name;
            return rename($path, $new_path);
        }

        public static function downloadFileFolder($full_path)
        {
            $type = self::fileOrFolder($full_path);
            if($type == 'file')
                return array(
                    'path' => $full_path,
                    'name' => pathinfo($full_path, PATHINFO_FILENAME) . '.' . pathinfo($full_path, PATHINFO_EXTENSION),
                    'type' => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $full_path)
                );
            else if($type == 'folder') {
                $folder = new Folder($full_path);
                return $folder->createZipArchive();
            }
            return null;
        }

        public static function fileOrFolder($path) {
            if(!file_exists($path))
                return 'File or folder not exists';
            return is_file($path) ? 'file' : 'folder';
        }

        public function uploadFiles($files, $allow_extension) {
            $uploaded_files = [];
            for($i = 0; $i < count($files['name']); $i++) {
                $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                if (in_array($file_extension, $allow_extension)) {
                    $file_path = rtrim($this->current_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $files['name'][$i];
                    if (!file_exists($file_path)) {
                        move_uploaded_file($files['tmp_name'][$i], $file_path);
                        $uploaded_files[] = $files['name'][$i];
                    }
                }
            }
            
            return $uploaded_files;
        }


        
        // Getters and setters
        public function getContents() { 
            return $this->contents; 
        }


    }

?>