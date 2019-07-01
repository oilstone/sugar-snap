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

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return bool
     */
    public function hasEntity()
    {
        return ($this->entity !== null);
    }

    /**
     * @return Resource|null
     */
    public function getResource()
    {
        return $this->entity instanceof Relation ? $this->entity->getForeignResource() : $this->entity;
    }

    /**
     * @return bool
     */
    public function isSingleton()
    {
        return ($this->getResource() instanceof Singleton);
    }

    /**
     * @return bool
     */
    public function isCollectable()
    {
        return ($this->getResource() instanceof Collectable);
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Pipe $pipe
     * @return $this
     */
    public function scope(Pipe $pipe)
    {
        $this->scope = new Scope($pipe, $this->entity);

        return $this;
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        if ($this->scope) {
            $this->scope->prepare();
        }

        $this->data = $this->getResource()->find($this->key);

        return $this;
    }

    /**
     * @param $request
     * @param $pipeline
     * @return $this
     */
    public function call($request, $pipeline)
    {
        if ($this->scope) {
            $this->scope->
        }

        switch ($request->method()) {
            case 'POST':
                $method = 'create';
                $arguments = [];
                break;
            case 'PUT';
                $method = 'update';
                $arguments = [];
                break;
            case 'DELETE';
                $method = 'destroy';
                $arguments = [];
                break;
            default:
                $method = implode('', ['get', $this->scope ? 'Scoped' : '', $this->key ? 'Item' : 'Collection']);
                $arguments = array_filter([$this->key, $this->scope, $request, $pipeline]);
        }

        $this->entity->{$method}(...$arguments);

        return $this;
    }

    public function method()
    {
        switch ($request->method()) {
            case 'POST':
                $method = 'create';
                break;
            case 'PUT';
                $method = 'update';
                break;
            case 'DELETE';
                $method = 'destroy';
                break;
            default:
                $method = implode('', ['get', $this->scope ? 'Scoped' : '', $this->key ? 'Item' : 'Collection']);
                $arguments = array_filter([$this->key, $this->scope, $request, $pipeline]);
        }
    }
}
