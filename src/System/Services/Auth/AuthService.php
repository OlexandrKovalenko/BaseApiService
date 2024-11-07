<?php

namespace App\System\Services\Auth;

use App\System\Repositories\User\UserRepository;
use App\System\Services\BaseService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Random\RandomException;

class AuthService extends BaseService implements AuthServiceInterface
{
    private UserRepository $userRepo;
    private string $secret;
    private int $accessExpiration;
    private int $refreshExpiration;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->secret = $_ENV['JWT_SECRET'];
        $this->accessExpiration = (int)$_ENV['JWT_EXPIRATION'];
        $this->refreshExpiration = (int)$_ENV['JWT_REFRESH_EXPIRATION'];
    }

    /**
     * @throws RandomException
     */
    public function generateTokens(int $userId): array
    {
        $accessToken = $this->generateAccessToken($userId);
        $refreshToken = $this->generateRefreshToken($userId);

        return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
    }

    private function generateAccessToken(int $userId): string
    {
        $payload = [
            'user_id' => $userId,
            'exp' => time() + $this->accessExpiration,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @throws RandomException
     */
    private function generateRefreshToken(int $userId): string
    {
        $refreshToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $this->refreshExpiration);

        //todo $this->userRepo->storeRefreshToken($userId, $refreshToken, $expiresAt);

        return $refreshToken;
    }

    public function validateToken(string $token): ?int
    {
        try {
/*            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded->user_id;*/
            return 1;
        } catch (\Exception $e) {
            return null;
        }
    }
}