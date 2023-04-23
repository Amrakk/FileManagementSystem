<?php
    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class Account 
    {
        private $id;
        private $username;
        private $password;
        private $created_date;
        private $is_activated;
        private $activate_token;

        public function __construct($id = null, $username = null, $password = null, 
                                    $created_date = null, $is_activated = null, $activate_token = null) 
        {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->created_date = $created_date;
            $this->is_activated = $is_activated;
            $this->activate_token = $activate_token;
        }

        public static function getAccountByUserName($username) 
        {
            $conn = new Database();
            $query = "SELECT * FROM account WHERE username = :username";
            $params = [
                'username' => $username
            ];
            $data = $conn->selectQuery($query, $params)[0] ?? null;
            if(empty($data)) return null;
            
            return new Account($data['id'], $data['username'], $data['password'], 
                               $data['created_date'], $data['is_activated'], $data['activate_token']);
        }

        public static function getAccountByID($id)
        {
            $conn = new Database();
            $query = "SELECT * FROM account WHERE id = :id";
            $params = [
                'id' => $id
            ];
            $data = $conn->selectQuery($query, $params)[0] ?? null;
            if(empty($data)) return null;
            
            return new Account($data['id'], $data['username'], $data['password'], 
                               $data['created_date'], $data['is_activated'], $data['activate_token']);
        }

        public static function deleteAccount($id)
        {
            $conn = new Database();
            $query = "DELETE FROM account WHERE id = :id";
            $params = [
                'id' => $id
            ];

            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }
        
        public static function generateID()
        {
            $conn = new Database();
            $query = "SELECT MAX(id) as maxID FROM account";
            $data = $conn->selectQuery($query)[0] ?? null;
            if(empty($data)) return "U001";

            $id = $data['maxID'];
            $id = (int)substr($id, 1,3);
            $newID = "U" . str_pad($id + 1, 3, "0", STR_PAD_LEFT);
            return $newID;
        }

        public static function isUserNameExisted($username)
        {
            $conn = new Database();
            $query = "SELECT * FROM account WHERE username = :username";
            $params = [
                'username' => $username
            ];

            $data = $conn->selectQuery($query, $params)[0] ?? null;
            if(empty($data)) return false;
            return true;
        }

        public static function isAccountActivated($username)
        {
            $conn = new Database();
            $query = "SELECT * FROM account WHERE username = :username AND is_activated = 1";
            $params = [
                'username' => $username
            ];

            $data = $conn->selectQuery($query, $params)[0] ?? null;
            if(empty($data)) return false;
            return true;
        }
    
        public function insertAccount()
        {
            $conn = new Database();
            $query = "INSERT INTO account (id, username, password, created_date, activate_token)
                      VALUES (:id, :username, :password, :created_date, :activate_token)";
            $params = [
                'id' => $this->id,
                'username' => $this->username,
                'password' => $this->password,
                'created_date' => $this->created_date,
                'activate_token' => $this->activate_token
            ];
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public function updateAccount()
        {
            $conn = new Database();
            $query = "UPDATE account SET username = :username, password = :password, created_date = :created_date, 
                      is_activated = :is_activated, activate_token = :activate_token WHERE id = :id";

            $params = [
                'id' => $this->id,
                'username' => $this->username,
                'password' => $this->password,
                'created_date' => $this->created_date,
                'is_activated' => $this->is_activated,
                'activate_token' => $this->activate_token
            ];
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public function getId() { return $this->id; }
        public function getUsername() { return $this->username; }
        public function getPassword() { return $this->password; }
        public function getCreated_date() { return $this->created_date; }
        public function getIs_activated() { return $this->is_activated; }
        public function getActivate_token() { return $this->activate_token; }
        

        public function setUsername($username): self {
            $this->username = $username;
            return $this;
        }

        public function setPassword($password): self {
            $this->password = $password;
            return $this;
        }
        
        public function setIs_activated($is_activated): self {
            $this->is_activated = $is_activated;
            return $this;
        }
        
        public function setActivate_token($activate_token): self {
            $this->activate_token = $activate_token;
            return $this;
        }
    }
?>