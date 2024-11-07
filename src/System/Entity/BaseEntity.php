<?php

namespace App\System\Entity;

use DateTime;

abstract class BaseEntity
{
    protected array $attributes = [];
    protected ?DateTime $created_at = null;
    protected ?DateTime $updated_at = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @throws \Exception
     */
    public function setAttribute($key, $value): void
    {
        if (property_exists($this, $key)) {
            if (in_array($key, ['created_at', 'updated_at']) && is_string($value)) {
                $this->$key = DateTime::createFromFormat('Y-m-d H:i:s', $value) ?: new DateTime($value);
            } else {
                $this->$key = $value;
            }
        } else {
            $this->attributes[$key] = $value;
        }
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getAllAttributes(): array
    {
        $properties = get_object_vars($this);
        return array_merge($this->attributes, $properties);
    }

    public function toArray(): array
    {
        return $this->getAllAttributes();
    }

    public function toJson(): false|string
    {
        return json_encode($this->toArray());
    }
}
