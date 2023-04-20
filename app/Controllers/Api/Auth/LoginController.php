<?php

    namespace App\Controllers\Api\Auth;
    
    use App\Models\Auth\Account;
    use App\Models\Auth\Profile;

    class LoginController {

        public static function verify($username, $password) {
            $result = Account::getAccountByUserName($username);

            if($result == null) {
                return json_encode(array('code' => 1, 'message' => 'Wrong username or password'));
            }

            if(!password_verify($password, $result->getPassword())) 
                return json_encode(array('code' => 2, 'message' => 'Wrong username or password'));


            return json_encode(array('code' => 0, 'message' => 'Login successful', 'data' => array('id' => $result->getID())));
        }

       
    }
?>