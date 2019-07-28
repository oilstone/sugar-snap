<?php

namespace Api;

use Closure;

/**
 * Class Registry
 * @package Api
 */
abstract class Registry extends Collection
{
    /**
     * @param string $name
     * @param Closure $callback
     */
    public function bind(string $name, Closure $callback)
    {
        $this->items[$name] = $callback;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return $this->offsetExists($name) ? $this->resolve($name) : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    abstract public function resolve(string $name);
}