<?php

namespace App\System\Util\Validator;

class RequestValidator
{
    private array $errors = [];

    /**
     * Перевірка на наявність обов'язкових параметрів у запиті
     *
     * @param array $data - масив даних з запиту
     * @param array $requiredFields - масив обов'язкових полів
     * @return bool
     */
    public function validate(array $data, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->addError($field, "$field is required.");
            }
        }

        return empty($this->errors);
    }

    /**
     * Додавання помилки
     *
     * @param string $field - ім'я поля
     * @param string $message - повідомлення про помилку
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Отримати помилки
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}