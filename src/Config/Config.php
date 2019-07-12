<?php

namespace Api\Config;

class Config
{
    protected $name;

    protected $accepts = [];

    protected $values = [];

    /**
     * Config constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
        return $this->values[$key] ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Config
     */
    public function __call($name, $arguments)
    {
        return $this->set($name, $arguments[0]);
    }
}