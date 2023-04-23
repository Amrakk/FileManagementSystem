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

        public function getFiles()
        {
            return $this->getFileOrFolder('file');
        }

        public function getFolders()
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

        public static function createFolder($path, $folder_name)
        {
            $folder_path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder_name;
            if (!file_exists($folder_path)) {
                return mkdir($folder_path);
            }
        }

        

        // public function addFileOrFolder() {

            
        // }
    
        // public function removeFileOrFolder($file_folder) {
        //     $index = array_search($file_folder, $this->files_folders);
        //     if ($index !== false) {
        //         array_splice($this->files_folders, $index, 1);
        //     }
        // }]



        // Static methods

        public static function fileOrFolder($path) {
            if(!file_exists($path))
                return 'File or folder not exists';
            return is_file($path) ? 'file' : 'folder';
        }


        
        // Tmp methods

        // public function create_folder($folder_id, $name, $size, $path, $modified_date, $owner_id) {
        //     $folder = new Folder($folder_id, $name, $size, $path, $modified_date, $owner_id);
        //     array_push($this->folders, $folder);
        //     return $folder;
        // }

        // public function create_file($file_id, $name, $size, $path, $modified_date, $extension, $owner_id) {
        //     $file = new File($file_id, $name, $size, $path, $modified_date, $extension, $owner_id);
        //     array_push($this->files, $file);
        //     return $file;
        // }

        // public function add_file_to_folder($file, $folder) {
        //     $folder->add_file($file);
        // }

        // public function remove_file_from_folder($file, $folder) {
        //     $folder->remove_file($file);
        // }

        // public function get_files_in_folder($folder) {
        //     return $folder->files;
        // }

        // Getters and setters
        public function getContents() { 

            return $this->contents; 
        }


    }

?>