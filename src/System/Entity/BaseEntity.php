<?php

namespace App\System\Entity;

use DateTime;
use Exception;

/**
 * Class BaseEntity
 *
 * @package App\System\Entity
 * @author maslo
 * @since 08.11.2024
 */
abstract class BaseEntity
{
    /**
     * @var array $attributes
     */
    protected array $attributes = [];
    /**
     * @var ?DateTime $created_at
     */
    protected ?DateTime $created_at = null;
    /**
     * @var ?DateTime $updated_at
     */
    protected ?DateTime $updated_at = null;

    /**
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * fill
     *
     * @param array $attributes
     * @return void
     * @throws Exception
     */
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * setAttribute
     *
     * @param $key
     * @param $value
     * @return void
     * @throws Exception
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

    /**
     * getAttribute
     *
     * @param $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * getAllAttributes
     *
     * @return array
     */
    public function getAllAttributes(): array
    {
        $properties = get_object_vars($this);
        return array_merge($this->attributes, $properties);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getAllAttributes();
    }

    /**
     * toJson
     *
     * @return false|string
     */
    public function toJson(): false|string
    {
        return json_encode($this->toArray());
    }
}
