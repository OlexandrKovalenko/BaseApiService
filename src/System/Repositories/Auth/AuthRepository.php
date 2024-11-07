<?php

namespace App\System\Repositories\Auth;

use App\System\Repositories\Auth\AuthRepositoryInterface;
use App\System\Repositories\BaseRepository;
use App\System\Util\GuidHelper;
use Exception;
use PDO;
use Random\RandomException;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    /**
     * @param int $userId
     * @param string $refreshToken
     * @param int $expiresAt
     * @return void
     * @throws Exception
     */
    public function storeRefreshToken(int $userId, $refreshToken, $expiresAt): void
    {
        $guid = GuidHelper::createLocalSessionId();

        $query = 'INSERT INTO user_tokens (user_id, refresh_token, expires_at, created_at) 
                  VALUES (:user_id, :refresh_token, :expires_at, NOW())
                  ON DUPLICATE KEY UPDATE 
                  refresh_token = :refresh_token, expires_at = :expires_at';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['auth', 'storeRefreshToken', 'query'],
        ]);

        $stmt = $this->db->prepare($query);

        $result = $stmt->execute([
            ':user_id' => $userId,
            ':refresh_token' => $refreshToken,
            ':expires_at' => date('Y-m-d H:i:s', $expiresAt)
        ]);

        if (!$result) {
            throw new Exception("Failed to store refresh token.");
        }
    }

    /**
     * @param int $userId
     * @param string $refreshToken
     * @return array|null
     * @throws RandomException
     */
    public function getRefreshToken($userId, $refreshToken): ?array
    {
        $guid = GuidHelper::createLocalSessionId();

        $query = 'SELECT * FROM user_tokens 
                  WHERE user_id = :user_id AND refresh_token = :refresh_token';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['auth', 'getRefreshToken', 'query'],
        ]);

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':refresh_token' => $refreshToken
        ]);

        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $tokenData ? $tokenData : null;
    }
}