<?php
    namespace App\Models\Shared;

    use App\Models\Shared\AppConfig;

    class Database {
        private $dbConnection = null;

        public function __construct()
        {
            
            $host = AppConfig::get('DB_HOST', 'localhost');
            $port = AppConfig::get('DB_PORT', '3306');
            $db   = AppConfig::get('DB_DATABASE', 'cloudstorage');
            $user = AppConfig::get('DB_USERNAME', 'root');
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
