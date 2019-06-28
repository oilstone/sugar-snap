<?php

namespace Api\Resources\Relations;

use Api\Registry;
use Api\Resources\Resource;

class Relation
{
    protected $localResource;

    protected $foreignResource;

    protected $name;

    protected $binding;

    public function __construct(Resource $localResource)
    {
        $this->localResource = $localResource;
    }

    public function getLocalResource()
    {
        return $this->localResource;
    }

    public function foreignResource(Resource $model)
    {
        $this->foreignResource = $model;

        return $this;
    }

    public function getForeignResource()
    {
        if ($this->foreignResource) {
            return $this->foreignResource;
        }

        if ($this->binding) {
            $this->foreignResource = Registry::get($this->binding);

            return $this->foreignResource;
        }

        return null;
    }

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function bind(string $name)
    {
        $this->binding = $name;

        return $this;
    }

    public function getBinding()
    {
        return $this->binding;
    }
}