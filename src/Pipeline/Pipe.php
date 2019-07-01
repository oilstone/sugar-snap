<?php

namespace Api\Pipeline;

use Api\Resources\Singleton;
use Api\Resources\Collectable;
use Api\Resources\Relations\Relation;

class Pipe
{
    protected $entity;

    protected $key;

    protected $method;

    protected $arguments = [];

    protected $scope;

    protected $data = [];

    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function hasEntity()
    {
        return ($this->entity !== null);
    }

    public function getResource()
    {
        return $this->entity instanceof Relation ? $this->entity->getForeignResource() : $this->entity;
    }

    public function isSingleton()
    {
        return ($this->getResource() instanceof Singleton);
    }

    public function isCollectable()
    {
        return ($this->getResource() instanceof Collectable);
    }

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function addArgument($argument)
    {
        $this->arguments = $argument;
    }

    public function scope($pipe)
    {
        $this->scope = new Scope($pipe, $this->entity);

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function call()
    {
        $this->entity->{$this->method}(...$this->arguments);

        return $this;
    }
}