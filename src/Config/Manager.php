<?php

namespace Api\Config;

use Api\Collection;

class Manager
{
    protected $services;

    protected $enabled;

    protected $parent;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->services = new Collection();
    }

    /**
     * @param string $name
     * @param Service $service
     * @return $this
     */
    public function service(string $name, Service $service)
    {
        $this->services->put($name, $service);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasService(string $name)
    {
        return $this->services->has($name);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getService(string $name)
    {
        return $this->hasService($name)
            ? $this->services->get($name)
            : ($this->parent ? $this->parent->getService($name) : null);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function use(string $name)
    {
        if ($this->hasService($name) || ($this->parent && $this->parent->hasService($name))) {
            $this->enabled = $name;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled ? $this->enabled : ($this->parent ? $this->parent->getEnabled() : null);
    }

    /**
     * @return mixed|null
     */
    public function getEnabledService()
    {
        return $this->getService($this->getEnabled());
    }

    /**
     * @param Manager $manager
     * @return $this
     */
    public function inherit(Manager $manager)
    {
        $this->parent = $manager;

        return $this;
    }

    /**
     * @return Manager
     */
    public function child()
    {
        return (new static())->inherit($this);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {
        if ($service = $this->getEnabledService()) {
            return $service->get($name);
        }

        return null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $enabled = $this->getEnabled();

        if ($this->services->has($enabled)) {
            $service = $this->services->get($enabled);
        } else {
            $service = $this->parent->getEnabledService()->child();
            $this->services->put($enabled, $service);
        }

        $service->{$name}(...$arguments);

        return $this;
    }
}
