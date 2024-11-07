<?php

namespace App\System\Repositories\User;

use App\System\Entity\User;
use App\System\Repositories\BaseRepository;
use App\System\Util\GuidHelper;
use Exception;
use PDO;
use Random\RandomException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    // Метод для збереження нового користувача
    /**
     * @throws Exception
     */
    public function store(User $user): int
    {
        $guid = GuidHelper::createLocalSessionId();
        $user->beforeSave(); // Оновлюємо created_at та updated_at

        $query = 'INSERT INTO users (first_name, last_name, phone, email, password, note, last_login, created_at, updated_at) 
                  VALUES (:first_name, :last_name, :phone, :email, :password, :note, :last_login, :created_at, :updated_at)';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['user', 'store', 'query'],
        ]);

        $stmt = $this->db->prepare($query);

        $result = $stmt->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':phone' => $user->getPhone(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':note' => $user->getNote(),
            ':last_login' => $user->getLastLogin() ? $user->getLastLogin() : null,
            ':created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ':updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);
        if ($result) {
            return $this->db->lastInsertId(); // Повертаємо ID нового користувача
        }

        throw new Exception("Failed to create user."); // Викидаємо помилку, якщо вставка не вдалася
    }

    // Метод для оновлення користувача

    /**
     * @throws Exception
     */
    public function update(User $user): User
    {
        $user->beforeSave(); // Оновлюємо updated_at

        $query = 'UPDATE users SET first_name = :first_name, last_name = :last_name, phone = :phone, email = :email,
                  password = :password, note = :note, last_login = :last_login, updated_at = :updated_at
                  WHERE id = :id';

        $stmt = $this->db->prepare($query);

        $result = $stmt->execute([
            ':id' => $user->getId(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':phone' => $user->getPhone(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':note' => $user->getNote(),
            ':last_login' => $user->getLastLogin() ? $user->getLastLogin() : null,
            ':updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);

        if ($result) {
            return $user;
        }

        throw new Exception("Failed to update user.");
    }

    // Метод для отримання користувача за ID

    /**
     * @throws Exception
     */
    public function findById(int $id): ?User
    {
        $guid = GuidHelper::createLocalSessionId();

        $query = 'SELECT * FROM users WHERE id = :id';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['user', 'findById', 'query'],
        ]);

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return new User($user);
        }

        throw new Exception("No user found.");
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function findByPhone(string $userPhone): ?User
    {
        $guid = GuidHelper::createLocalSessionId();

        $query = 'SELECT * FROM users WHERE phone = :phone';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['user', 'findByPhone', 'query'],
        ]);

        $stmt = $this->db->prepare($query);
        $stmt->execute([':phone' => $userPhone]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return new User($user);
        }

        throw new Exception("No user found.");
    }
}
