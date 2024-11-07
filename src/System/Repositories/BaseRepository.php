<?php
namespace App\System\Repositories;

use App\System\Traits\LoggableTrait;
use App\System\Util\GuidHelper;
use App\System\Util\Log;
use PDO;
use App\System\Core\DbConnection;
use Random\RandomException;

abstract class BaseRepository
{
    use LoggableTrait;
    protected string $globalSessionId;
    protected PDO $db;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $dbConnection = new DbConnection();
        $this->db = $dbConnection->getConnection();
        $this->globalSessionId = GuidHelper::getOrCreateGlobalSessionId();
        Log::init('QueryRepository');
    }
}
