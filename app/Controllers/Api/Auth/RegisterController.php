<?php

    namespace App\Controllers\Api\Auth;
    
    use App\Models\Auth\Account;
    use App\Models\Auth\Profile;

    class RegisterController {

        public static function register($first_name,$last_name,$email,$username,$password)
        {
            if (Profile::isEmailExisted($email)) return json_encode(array("code" => 2, "message" => "Email already exists"));
            if (Account::isUserNameExisted($username)) return json_encode(array("code" => 1, "message" => "Username already exists"));

            $rand = rand(0, 1000000);
            $id = Account::generateID();
            $name = $first_name . ' ' . $last_name;
            $activateToken = md5($username . $rand . $password);
            $password = password_hash($password, PASSWORD_BCRYPT);

            $profile = new Profile($id, $name, $email, "");
            $acc = new Account($id, $username, $password, date("Y-m-d"), null, $activateToken);
            if(!$acc->insertAccount()) return json_encode(array("code" => 10,"message" => "Signup failed"));

            if(!$profile->insertProfile()) {
                if(!Account::deleteAccount($id)) return json_encode(array("code" => 11,"message" => "Signup failed"));
                return json_encode(array("code" => 10,"message" => "Signup failed"));
            }
            return json_encode(array("code" => 0,"message" => "Signup success"));
        }
    }
?>