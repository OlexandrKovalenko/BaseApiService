<?php

namespace App\System\Entity;

use Exception;
use JsonSerializable;
use DateTime;

class User extends BaseEntity implements JsonSerializable
{
    protected int $id;
    protected string $first_name;
    protected string $last_name;
    protected string $phone;
    protected ?string $email;
    protected string $password;
    protected ?string $note;
    protected ?string $last_login;
    protected ?DateTime $created_at;
    protected ?DateTime $updated_at;

    /**
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!empty($attributes)) {
            //$this->id = $attributes['id'] ?? $this->id;
            $this->first_name = $attributes['first_name'] ?? $this->first_name;
            $this->last_name = $attributes['last_name'] ?? $this->last_name;
            $this->phone = $attributes['phone'] ?? $this->phone;
            $this->email = $attributes['email'] ?? $this->email;
            $this->password = $attributes['password'] ?? $this->password;
            $this->note = $attributes['note'] ?? $this->note;
            $this->last_login = $attributes['last_login'] ?? null;
            $this->created_at = isset($attributes['created_at']) ? new DateTime($attributes['created_at']) : null;
            $this->updated_at = isset($attributes['updated_at']) ? new DateTime($attributes['updated_at']) : null;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'note' => $this->note,
            'last_login' => $this->last_login,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function beforeSave(): void
    {
        $now = new DateTime();

        if ($this->created_at === null) {
            $this->created_at = $now;
        }

        $this->updated_at = $now;
    }

    // Оновлені геттери та сеттери для інших полів
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getLastLogin(): ?string
    {
        return $this->last_login;
    }

    public function setLastLogin(?string $last_login): void
    {
        $this->last_login = $last_login;
    }
}
