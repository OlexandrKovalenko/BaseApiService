<?php

namespace App\System\Core;

use Dotenv\Dotenv;
use PDO;

/**
 * Class DbConnection
 *
 * @package App\System\Core
 * @author maslo
 * @since 08.11.2024
 */
class DbConnection
{
    /**
     * @var PDO $connection
     */
    private PDO $connection;

    /**
     *
     */
    public function __construct()
    {
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

    /**
     * getConnection
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}