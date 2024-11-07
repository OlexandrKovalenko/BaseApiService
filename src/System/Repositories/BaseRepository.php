<?php
namespace App\System\Repositories;

use PDO;
use App\System\Core\DbConnection;

abstract class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        // Отримуємо підключення до бази даних
        $dbConnection = new DbConnection();
        $this->db = $dbConnection->getConnection();
    }
}
