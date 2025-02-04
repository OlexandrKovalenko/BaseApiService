<?php

namespace App\System\Repositories\User;

use App\System\Entity\User;
use App\System\Exception\UserException;
use App\System\Exception\UserNotFoundException;
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

    /**
     * findById
     *
     * @param int $id
     * @return User
     * @throws RandomException
     * @throws UserException
     */
    public function findById(int $id): User
    {
        $guid = GuidHelper::createLocalSessionId();
        $query = 'SELECT * FROM users WHERE id = :id';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['user', 'findById', 'query'],
            'user_id' => $id,
        ]);

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->logError($guid, "User with id $id not found.", [
                'tags' => ['user', 'findByPhone', 'not_found'],
            ]);
            throw UserException::userNotFound();
        }
        return new User($user);
    }

    /**
     * findByPhone
     *
     * @param string $userPhone
     * @return User
     * @throws RandomException
     * @throws UserException
     * @throws Exception
     */
    public function findByPhone(string $userPhone): User
    {
        $guid = GuidHelper::createLocalSessionId();

        $query = 'SELECT * FROM users WHERE phone = :phone';

        $this->logInfo($guid, (string)json_encode($query), [
            'tags' => ['user', 'findByPhone', 'query'],
            'user_id' => $userPhone,
        ]);

        $stmt = $this->db->prepare($query);
        $stmt->execute([':phone' => $userPhone]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->logError($guid, "User with phone $userPhone not found.", [
                'tags' => ['user', 'findByPhone', 'not_found'],
            ]);
            throw UserException::userNotFound();
        }
        return new User($user);
    }
}
