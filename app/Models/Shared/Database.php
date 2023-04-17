<?php
    namespace App\Models\Shared;
    use App\Models\Shared\AppConfig;
  


    class Database {
        private $dbConnection = null;

        public function __construct()
        {
            
            $host = AppConfig::get('DB_HOST', '');
            $port = AppConfig::get('DB_PORT', '');
            $db   = AppConfig::get('DB_DATABASE', '');
            $user = AppConfig::get('DB_USERNAME', '');
            $pass = AppConfig::get('DB_PASSWORD', '');

            try {
                $this->dbConnection = new \PDO("mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db", $user, $pass);
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }

        public function selectQuery($query, $params = [])
        {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function actionQuery($query, $params = [])
        {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute($params);

            return $stmt->rowCount();
        }

    }
?>
