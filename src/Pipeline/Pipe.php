<?php

namespace Api\Pipeline;

class Pipe
{
    protected $entity;

    protected $key;

    protected $method;

    protected $arguments = [];

    protected $scope;

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

    public function setScope($pipe)
    {
        $this->scope = new Scope($pipe, $this);

        return $this;
    }

    public function call()
    {



        $this->entity->{$this->method}(...$this->arguments);

        return $this;
    }
}