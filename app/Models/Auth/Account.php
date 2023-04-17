<?php
    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class Account {
        
        
        public function getAccountByUserName($username) {
            $conn = new Database();
            $data = $conn->selectQuery("SELECT * FROM accounts WHERE username = ?", [$username]);
            return $data;
        }
    }
   





?>