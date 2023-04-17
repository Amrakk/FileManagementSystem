<?php
    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class Account {
        public $test = new Database();


        public function getAccountByUserName($username) {
            $conn = new Database();
            $query = "SELECT * FROM accounts WHERE username = :username";
            $params = [
                'username' => $username
            ];

            
            $data = $conn->selectQuery($query, $params);
            return $data;
        }
    }
   





?>