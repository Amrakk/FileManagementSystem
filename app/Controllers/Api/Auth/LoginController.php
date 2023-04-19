<?php

    namespace App\Controllers\Api\Auth;
    use App\Models\Auth\Account;

    class LoginController {

        public static function verify($username, $password) {
            $account = Account::getAccountByUserName($username);

            if($account->getId() == null) {
                return json_encode(array('code' => 1, 'message' => 'Wrong username or password'));
            }

            if($password == $account->getPassword()) {
                return json_encode(array('code' => 0, 'message' => 'Login successful'));
            } else {
                return json_encode(array('code' => 2, 'message' => 'Wrong username or password'));
            }

        }
    }
?>