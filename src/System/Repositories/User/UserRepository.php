<?php

namespace App\System\Repositories\User;

use App\System\Entity\User;
use App\System\Repositories\BaseRepository;
use DateTime;
use Exception;
use PDO;

class UserRepository extends BaseRepository
{
    // Метод для збереження нового користувача
    /**
     * @throws Exception
     */
    public function store(User $user): int
    {
        $user->beforeSave(); // Оновлюємо created_at та updated_at

        $query = 'INSERT INTO users (first_name, last_name, phone, email, password, note, last_login, created_at, updated_at) 
                  VALUES (:first_name, :last_name, :phone, :email, :password, :note, :last_login, :created_at, :updated_at)';

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

        throw new Exception("Не вдалося створити користувача."); // Викидаємо помилку, якщо вставка не вдалася
    }

    // Метод для оновлення користувача
    public function update(User $user): void
    {
        $user->beforeSave(); // Оновлюємо updated_at

        $query = 'UPDATE users SET first_name = :first_name, last_name = :last_name, phone = :phone, email = :email,
                  password = :password, note = :note, last_login = :last_login, updated_at = :updated_at
                  WHERE id = :id';

        $stmt = $this->db->prepare($query);

        $stmt->execute([
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
    }

    // Метод для отримання користувача за ID
    public function findById(int $id): ?User
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return new User($user);
        }

        return null;
    }
}
