<?php

    namespace App\Controllers\Api\Auth;
    
    use App\Models\Auth\Account;
    use App\Models\Auth\Profile;
    use App\Models\Auth\ResetToken;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;

    class LoginController {

        public static function verify($username, $password) {
            $result = Account::getAccountByUserName($username);

            if($result == null) {
                return json_encode(array('code' => 1, 'message' => 'Wrong username or password'));
            }

            if(!password_verify($password, $result->getPassword())) 
                return json_encode(array('code' => 2, 'message' => 'Wrong username or password'));

            if($result-> isAccountActivated($username) == false)
                    return json_encode(array('code' => 3, 'message' => 'Account is not activated'));

            return json_encode(array('code' => 0, 'message' => 'Login successful', 'data' => array('id' => $result->getID())));
        }

        public static function forgot($email) {
            if(Profile::isEmailExisted($email) == false) {
                return json_encode(array('code' => 1, 'message' => 'Email is not existed'));
            }
            $token = md5($email . rand(1000, 9999));
            if(ResetToken::updateToken($email, $token) == false) {
                $newResetToken = new ResetToken();
                $expired = date('Y/m/d H:i:s',time() +60 * 60 * 24);
                $newResetToken->setEmail($email);
                $newResetToken->setToken($token);
                $newResetToken->setExpireOn($expired);
                if($newResetToken->insertResetToken())
                {
                    if(self::sendResetEmail($email, $token))
                        return json_encode(array('code' => 0, 'message' => 'Reset token is sent to your email'));
                    else return json_encode(array('code' => 2, 'message' => 'Cannot send reset token to your email'));    
                }
                else return json_encode(array('code' => 2, 'message' => 'Cannot send reset token to your email'));
            }else {
                self::sendResetEmail($email, $token);
                return json_encode(array('code' => 0, 'message' => 'Reset token is sent to your email'));
            }
        }

        public static function sendResetEmail($email, $token) {
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
                $mail->Subject = 'Reset password';
                $mail->Body    = "Click <a href='http://localhost/Public/Pages/Auth/reset_password.php?email=$email&token=$token'>here</a> to reset password";
    
                return $mail->send();
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
        public static function resetPassword($email, $token, $password) {
            $data = ResetToken::getResetToken($email);
            if($data == null)
                return json_encode(array("code" => 3,"message" => "Email is not correct"));
            else
            {
                if($data->getToken() != $token)
                return json_encode(array("code" => 3,"message" => "Token is not correct"));
                else if($data->getExpireOn() > date('Y/m/d H:i:s')){
                    return json_encode(array("code" => 3,"message" => "Token is expired"));
                }
                else
                {
                    $data->setToken(null);
                    $data->setExpireOn(null);
                    $id = Profile::getProfileByEmail($email)->getId();
                    $acc = Account::getAccountByID($id);
                    $acc->setPassword(password_hash($password, PASSWORD_BCRYPT));
                    if(!$acc->updateAccount() || !$data->updateResetToken()) return json_encode(array("code" => 3,"message" => "Reset password failed"));
                    return json_encode(array("code" => 0,"message" => "Reset password successfully"));
                }
            }
        }

       
    }
?>