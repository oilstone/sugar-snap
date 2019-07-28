<?php

namespace Api\Config;

use Closure;

class Config
{
    protected $name;

    protected $accepts = [];

    protected $values = [];

    protected $parent;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name ? $this->name : $this->parent ? $this->parent->getName() : '';
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    public function accepts(...$arguments)
    {
        $this->accepts = array_unique(array_merge($this->accepts, $arguments));

        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function inherit(Config $config)
    {
        $this->parent = $config;

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        if (in_array($key, $this->accepts)) {
            $this->values[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $value = array_key_exists($key, $this->values)
            ? $this->values[$key]
            : $this->parent ? $this->parent->get($key) : null;

        if ($value instanceof Closure) {
            $value = $value();
            $this->set($key, $value);
        }

        return $value;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Config
     */
    public function __call($name, $arguments)
    {
        return $this->set($name, $arguments ? $arguments[0] : true);
    }
}