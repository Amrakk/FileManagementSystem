<?php
    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class Account {
        private $id;
        private $username;
        private $password;
        private $created_date;
        private $is_active;
        private $activate_token;

        public function __construct($id = null, $username = null, $password = null, $created_date = null, $is_active = null, $activate_token = null) {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->created_date = $created_date;
            $this->is_active = $is_active;
            $this->activate_token = $activate_token;
        }

        public static function getAccountByUserName($username) {
            $conn = new Database();
            $query = "SELECT * FROM user WHERE username = :username";
            $params = [
                'username' => $username
            ];
            $data = $conn->selectQuery($query, $params)[0] ?? null;
            
            if($data == null) return new Account();
            
            return new Account($data['userID'], $data['username'], $data['password'], 
                                   $data['createdDate'], $data['IsActived'], $data['activateToken']);

        }
    
        
        public function getId() {
            return $this->id;
        }
        
        public function setId($id): self {
            $this->id = $id;
            return $this;
        }
        
        public function getUsername() {
            return $this->username;
        }
        
        public function setUsername($username): self {
            $this->username = $username;
            return $this;
        }
        
        public function getPassword() {
            return $this->password;
        }
        
        public function setPassword($password): self {
            $this->password = $password;
            return $this;
        }
        
        public function getCreated_date() {
            return $this->created_date;
        }
        
        public function setCreated_date($created_date): self {
            $this->created_date = $created_date;
            return $this;
        }
        
        public function getIs_active() {
            return $this->is_active;
        }
        
        public function setIs_active($is_active): self {
            $this->is_active = $is_active;
            return $this;
        }
        
        public function getActivate_token() {
            return $this->activate_token;
        }
        
        public function setActivate_token($activate_token): self {
            $this->activate_token = $activate_token;
            return $this;
        }
    }
    
?>