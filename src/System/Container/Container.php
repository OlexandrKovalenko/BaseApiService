<?php

namespace App\System\Container;

use Exception;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, callable $resolver): void
    {
        $this->bindings[$abstract] = $resolver;
    }

    /**
     * @throws Exception
     */
    public function make(string $abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("No binding found for {$abstract}");
        }

        return $this->bindings[$abstract]($this);
    }
}
