<?php
    namespace App\Models\Storage;

    class Folder
    {
        private $name;
        private $size;
        private $modified_date;
        private $path;

        public function __construct($folder_path) 
        {
            $this->name = basename($folder_path);
            $this->size = $this->getFolderSize($folder_path);
            $this->modified_date = date('Y-m-d', filemtime($folder_path));
            $this->path = $folder_path;
        }

        private function getFolderSize($folder_path)
        {
            $total_size = 0;
            $files = glob(rtrim($folder_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $total_size += filesize($file);
                } else {
                    $total_size += $this->getFolderSize($file);
                }
            }
            return $total_size;
        }

        public function getInfo()
        {
            return [
                'name' => $this->name,
                'size' => $this->size,
                'modified_date' => $this->modified_date,
                'path' => $this->path
            ];
        }

        





        // Getters and Setters

        public function getName() { return $this->name; }
        public function getSize() { return $this->size; }
        public function getModifiedDate() { return $this->modified_date; }
        public function getPath() { return $this->path; }


        public function setName($name): self { 
            $this->name = $name; 
            return $this;
        }

        public function setSize($size): self { 
            $this->size = $size; 
            return $this;
        }

        public function setModifiedDate($modified_date): self { 
            $this->modified_date = $modified_date; 
            return $this;
        }


        public function setPath($path): self { 
            $this->path = $path; 
            return $this;
        }
    }
?>