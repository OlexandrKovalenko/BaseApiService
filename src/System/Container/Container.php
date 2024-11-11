<?php
declare(strict_types=1);

namespace App\System\Container;

use Exception;

/**
 * Class Container
 *
 *
 * @package App\System\Container
 * @author maslo
 * @since 08.11.2024
 */
class Container
{
    private array $bindings = [];

    /**
     * @param string $abstract
     * @param callable $resolver
     * @throws Exception
     */
    public function bind(string $abstract, callable $resolver): void
    {
        $this->bindings[$abstract] = $resolver;
    }

    /**
     * make a new instance of the given abstract type from the container.
     *
     * @param string $abstract
     * @return mixed
     * @throws Exception
     */
    public function make(string $abstract): mixed
    {
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("No binding found for {$abstract}");
        }

        return $this->bindings[$abstract]($this);
    }
}
