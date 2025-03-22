<?php

namespace Database;

class Database {
    private static ?\PDO $instance = null;
    private static string $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=sandstorm;charset=utf8';
    private static string $username = 'root';
    private static string $password = '';

    private function __construct() {}

    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new \PDO(self::$dsn, self::$username, self::$password);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                die('Erreur de connexion : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private function __clone() {}
    
    public function __wakeup() {
        throw new \Exception("Cannot deserialize a singleton.");
    }
}
