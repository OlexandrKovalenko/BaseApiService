<?php

namespace App\System\Services\Auth;

use App\System\Entity\User;
use App\System\Repositories\Auth\AuthRepositoryInterface;
use App\System\Repositories\User\UserRepository;
use App\System\Services\BaseService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Random\RandomException;

/**
 * Class AuthService
 *
 * @package App\System\Services\Auth
 * @author maslo
 * @since 11.11.2024
 */
class AuthService extends BaseService implements AuthServiceInterface
{
    /**
     * @var AuthRepositoryInterface $authRepository
     */
    private AuthRepositoryInterface $authRepository;
    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;
    /**
     * @var string $secret
     */
    private string $secret;
    /**
     * @var int $accessExpiration
     */
    private int $accessExpiration;
    /**
     * @var int $refreshExpiration
     */
    private int $refreshExpiration;

    /**
     * @param AuthRepositoryInterface $authRepository
     * @param UserRepository $userRepository
     */
    public function __construct(AuthRepositoryInterface $authRepository, UserRepository $userRepository)
    {
        $this->authRepository = $authRepository;
        $this->userRepository = $userRepository;
        $this->secret = $_ENV['JWT_SECRET'];
        $this->accessExpiration = (int)$_ENV['JWT_EXPIRATION'];
        $this->refreshExpiration = (int)$_ENV['JWT_REFRESH_EXPIRATION'];
    }

    /**
     * generateTokens
     *
     * @param int $userId
     * @return array
     * @throws RandomException
     */
    public function generateTokens(int $userId): array
    {
        $accessToken = $this->generateAccessToken($userId);
        $refreshToken = $this->generateRefreshToken($userId);

        return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
    }

    /**
     * generateAccessToken
     *
     * @param int $userId
     * @return string
     */
    public function generateAccessToken(int $userId): string
    {
        $payload = [
            'user_id' => $userId,
            'exp' => time() + $this->accessExpiration,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * generateRefreshToken
     *
     * @param int $userId
     * @return string
     * @throws RandomException
     */
    private function generateRefreshToken(int $userId): string
    {
        $refreshToken = bin2hex(random_bytes(32));
        $expiresAt = time() + $this->refreshExpiration;
        $this->authRepository->storeRefreshToken($userId, $refreshToken, $expiresAt);

        return $refreshToken;
    }

    /**
     * validateToken
     *
     * @param string $token
     * @return int|null
     */
    public function validateToken(string $token): ?int
    {
        $token = str_replace('Bearer ', '', $token);
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded->user_id;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * validateRefreshToken
     *
     * @param int $userId
     * @param string $refreshToken
     * @return bool
     */
    public function validateRefreshToken(int $userId, string $refreshToken): bool
    {
        $storedToken = $this->authRepository->getRefreshToken($userId, $refreshToken);
        return $storedToken !== null && $storedToken['expires_at'] > time();
    }

    /**
     * refreshTokens
     *
     * @param int $userId
     * @param string $refreshToken
     * @return array|null
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