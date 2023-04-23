<?php

    namespace App\Controllers\Api\Auth;
    
    use app\Models\Shared\AppConfig;
    use App\Models\Auth\Account;
    use App\Models\Auth\Profile;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;

    class RegisterController {

        public static function register($first_name, $last_name, $email, $username, $password)
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
            if(!$acc->insertAccount()) return json_encode(array("code" => 10,"message" => "Add account failed"));

            if(!$profile->insertProfile()) {
                if(!Account::deleteAccount($id)) return json_encode(array("code" => 11,"message" => "Delete account failed"));
                return json_encode(array("code" => 10,"message" => "Add profile failed"));
            }

            if(!file_exists(AppConfig::get('STORAGE_PATH') . '/' . $id))
                mkdir(AppConfig::get('STORAGE_PATH') . '/' . $id);
                
            if(self::sendActivationMail($email,$activateToken))
                return json_encode(array("code" => 0,"message" => "Signup success", "data" => array("id" => $id)));
            else return json_encode(array("code" => 12,"message" => "Something went wrong"));
        }

        public static function sendActivationMail($email,$token){
            $mail = new PHPMailer(true);
            try {
                //Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = '94.nguyenhonhatnam@gmail.com';                     //SMTP username
                $mail->Password   = 'qtfnrpgshgxteezv';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
                //Recipients
                $mail->setFrom('94.nguyenhonhatnam@gmail.com', 'Cloud Storage');
                $mail->addAddress($email, 'Receiver');     //Add a recipient
    
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Confirm to activate your account';
                $mail->Body    = "Click <a href='http://localhost/Public/Pages/Auth/Activate.php?email=$email&token=$token'>here</a> to activate your account";
    
                return $mail->send();;
            } catch (Exception $e) {
               return false;
            }
        }
        public static function activateAccount($email, $token){
            if(!Profile::isEmailExisted($email)) 
                return json_encode(array("code" => 3,"message" => "Activate account failed"));
            else
            {
                $profile = Profile::getProfileByEmail($email);
                $acc = Account::getAccountByID($profile->getId());
                if($acc->getActivate_token() != $token)
                return json_encode(array("code" => 3,"message" => "Activate account failed"));
                else
                {
                    $acc->setActivate_token(null);
                    $acc->setIs_activated(1);
                    if(!$acc->updateAccount()) return json_encode(array("code" => 3,"message" => "Activate account failed"));
                    return json_encode(array("code" => 0,"message" => "Activate successfully"));
                }
            }
        }
    }
?>