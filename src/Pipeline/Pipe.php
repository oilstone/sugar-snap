<?php

namespace Api\Pipeline;

use Api\Requests\Request;
use Api\Resources\Singleton;
use Api\Resources\Collectable;
use Api\Resources\Relations\Relation;

class Pipe
{
    protected $request;

    protected $entity;

    protected $key;

    protected $method;

    protected $arguments = [];

    protected $scope;

    protected $data = [];

    /**
     * Pipe constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
     * @return bool
     */
    public function hasKey()
    {
        return ($this->key !== null);
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
     * @param $pipeline
     * @return $this
     */
    public function call($pipeline)
    {
        $this->data = $this->getResource()->{$this->resolveMethod($pipeline)}(...$this->resolveArguments($pipeline));

        return $this;
    }

    /**
     * @param Pipeline $pipeline
     * @return array
     */
    protected function resolveArguments(Pipeline $pipeline)
    {
        return array_filter([$this->key, $this->scope, $this->request, $pipeline]);
    }

    /**
     * @param Pipeline $pipeline
     * @return string
     */
    protected function resolveMethod(Pipeline $pipeline)
    {
        switch ($this->request->method()) {
            case 'POST':
                return 'create';
            case 'PUT';
                return 'update';
            case 'DELETE';
                return 'destroy';
            default:
                if ($pipeline->current() === $this) {
                    return implode('', ['get', $this->scope ? 'Scoped' : '', $this->key ? 'Record' : 'Collection']);
                }

                return implode('', ['get', $this->scope ? 'Scoped' : '', 'ByKey']);
        }
    }
}
