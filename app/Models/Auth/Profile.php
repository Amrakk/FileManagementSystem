<?php

    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class Profile
    {
        private $id;
        private $name;
        private $email;
        private $phone_number;
        private $role;

        public function __construct($id = null, $name = null, $email = null, $phone_number = null, $role = null)
        {
            $this->id = $id;
            $this->name = $name;
            $this->email = $email;
            $this->phone_number = $phone_number;
            $this->role = $role;
        }

        public static function getAllProfile() {
            $conn = new Database();
            $query = "SELECT * FROM profile";
            $data = $conn->selectQuery($query);

            if(empty($data)) return null;

            $profiles = [];
            foreach($data as $profile) {
                $profile = new Profile($profile['id'], $profile['name'], $profile['email'], $profile['phone_number'], $profile['role']);
                array_push($profiles, $profile);
            }

            return $profiles;
        }

        public static function getProfileByID($id)
        {
            $conn = new Database();
            $query = "SELECT * FROM profile WHERE id = :id";
            $params = [
                'id' => $id
            ];

            $data = $conn->selectQuery($query, $params);
            if(empty($data)) return null;

            $profile = new Profile($data['id'], $data['name'], $data['email'], $data['phone_number'], $data['role']);
            return $profile;
        }
        
        public function insertProfile()
        {
            $conn = new Database();
            $query = "INSERT INTO profile (id, name, email, phone_number) 
                      VALUES (:id, :name, :email, :phone_number)";
            $params = [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number
            ];

            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }
        
        public function updateProfileByUserID()
        {
            $conn = new Database();
            $query = "UPDATE profile SET name = :name, email = :email, phone = :phone, role = :role WHERE id = :id";
            $params = [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone_number,
                'role' => $this->role
            ];
            
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }
        
        public static function deleteProfile($id)
        {
            $conn = new Database();
            $query = "DELETE FROM profile WHERE id = :id";
            $params = [
                'id' => $id
            ];

            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public static function isEmailExisted($email) {
            $conn = new Database();
            $query = "SELECT * FROM profile WHERE email = :email";
            $params = [
                'email' => $email
            ];

            $data = $conn->selectQuery($query, $params);
            return ($data == null) ? false : true;
        }

        public function getId() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }
        
        public function setName($name): self {
            $this->name = $name;
            return $this;
        }
        
        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email): self {
            $this->email = $email;
            return $this;
        }

        public function getPhone_number() {
            return $this->phone_number;
        }
        
        public function setPhone_number($phone_number): self {
            $this->phone_number = $phone_number;
            return $this;
        }
        
        public function getRole() {
            return $this->role;
        }
        
        public function setRole($role): self {
            $this->role = $role;
            return $this;
        }
    }
?>
