<?php

namespace App\System\Services\Auth;

use App\System\Entity\User;
use App\System\Repositories\Auth\AuthRepositoryInterface;
use App\System\Repositories\User\UserRepository;
use App\System\Services\BaseService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Random\RandomException;

class AuthService extends BaseService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepository;
    private UserRepository $userRepository;
    private string $secret;
    private int $accessExpiration;
    private int $refreshExpiration;

    public function __construct(AuthRepositoryInterface $authRepository, UserRepository $userRepository)
    {
        $this->authRepository = $authRepository;
        $this->userRepository = $userRepository;
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
        //$expiresAt = date('Y-m-d H:i:s', time() + $this->refreshExpiration);
        $expiresAt = time() + $this->refreshExpiration;
        $this->authRepository->storeRefreshToken($userId, $refreshToken, $expiresAt);

        return $refreshToken;
    }

    public function validateToken(string $token): ?int
    {
        $token = str_replace('Bearer ', '', $token);
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded->user_id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function validateRefreshToken(int $userId, string $refreshToken): bool
    {
        $storedToken = $this->authRepository->getRefreshToken($userId, $refreshToken);
        return $storedToken !== null && $storedToken->expires_at > time();
    }

    /**
     * @throws RandomException
     */
    public function refreshTokens(int $userId, string $refreshToken): ?array
    {
        if ($this->validateRefreshToken($userId, $refreshToken)) {
            return $this->generateTokens($userId);
        }
        return null;
    }
}