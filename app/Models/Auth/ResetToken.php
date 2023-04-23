<?php
    namespace App\Models\Auth;

    use App\Models\Shared\Database;

    class ResetToken{
        private $email;
        private $token;
        private $expire_on;
        public function __construct($email = null, $token = null, $expire_on = null)
        {
            $this->email = $email;
            $this->token = $token;
            $this->expire_on = $expire_on;
        }

        public static function getResetToken($email)
        {
            $conn = new Database();
            $query = "SELECT * FROM reset_token WHERE email = :email";
            $params = [
                'email' => $email
            ];
            $data = $conn->selectQuery($query, $params)[0] ?? null;
            if(empty($data)) return null;
            return new ResetToken($data['email'], $data['token'], $data['expire_on']);
        }

        public function insertResetToken()
        {
            $conn = new Database();
            $query = "INSERT INTO reset_token (email, token, expire_on) VALUES (:email, :token, :expire_on)";
            $params = [
                'email' => $this->email,
                'token' => $this->token,
                'expire_on' => $this->expire_on
            ];
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public function updateResetToken()
        {
            $conn = new Database();
            $query = "UPDATE reset_token SET token = :token, expire_on = :expire_on WHERE email = :email";
            $params = [
                'email' => $this->email,
                'token' => $this->token,
                'expire_on' => $this->expire_on
            ];
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public static function updateToken($email,$token)
        {
            $conn = new Database();
            $query = "UPDATE reset_token SET token = :token WHERE email = :email";
            $params = [
                'email' => $email,
                'token' => $token
            ];
            $data = $conn->actionQuery($query, $params);
            return ($data == 0) ? false : true;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function getToken()
        {
            return $this->token;
        }

        public function getExpireOn()
        {
            return $this->expire_on;
        }

        public function setEmail($email): self {
            $this->email = $email;
            return $this;
        }

        public function setToken($token): self {
            $this->token = $token;
            return $this;
        }

        public function setExpireOn($expire_on): self {
            $this->expire_on = $expire_on;
            return $this;
        }
    }
