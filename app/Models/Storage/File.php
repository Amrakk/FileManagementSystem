<?php
    namespace App\Models\Storage;

    class File 
    {
        private $name;
        private $size;
        private $modified_date;
        private $extension;
        private $path;

        public function __construct($file_path) 
        {
            $this->name = basename($file_path);
            $this->size = filesize($file_path);
            $this->modified_date = date('Y-m-d', filemtime($file_path));
            $this->extension = pathinfo($file_path, PATHINFO_EXTENSION);
            $this->path = $file_path;
        }


        public function getData() {
            header('Content-Type: '. finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('X-Content-Type-Options: nosniff');

            return file_get_contents($this->path);
        }

        public function getInfo() {
            return array(
                'name' => $this->name,
                'size' => $this->size,
                'modified_date' => $this->modified_date,
                'extension' => $this->extension,
                'path' => $this->path
            );
        }

        public function getFile() {
            return readfile($this->path);
        }
        
        // Getters and Setters

        public function getName() { return $this->name; }
        public function getSize() { return $this->size; }
        public function getModifiedDate() { return $this->modified_date; }
        public function getExtension() { return $this->extension; }
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

        public function setExtension($extension): self { 
            $this->extension = $extension; 
            return $this;
        }

        public function setPath($path): self { 
            $this->path = $path; 
            return $this;
        }

    }

?>