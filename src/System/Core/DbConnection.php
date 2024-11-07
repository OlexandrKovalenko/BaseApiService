<?php

namespace App\System\Core;

use Dotenv\Dotenv;
use PDO;

class DbConnection
{
    private $connection;

    public function __construct()
    {
        // Завантаження конфігурацій з .env файлу
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $port = $_ENV['DB_PORT'];

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;port=$port";
            $this->connection = new PDO($dsn, $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Could not connect to the database $dbname :" . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}