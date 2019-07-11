<?php

namespace Api\Pipeline;

use Psr\Http\Message\ServerRequestInterface;
use Api\Resources\Singleton;
use Api\Resources\Collectable;
use Api\Resources\Relations\Relation;
use Api\Resources\Resource;

class Pipe
{
    protected $request;

    protected $pipeline;

    protected $entity;

    protected $key;

    protected $scope;

    protected $data = [];

    /**
     * Pipe constructor.
     * @param Pipeline $pipeline
     * @param ServerRequestInterface $request
     */
    public function __construct(Pipeline $pipeline, ServerRequestInterface $request)
    {
        $this->pipeline = $pipeline;
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
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
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
     * @return bool
     */
    public function isScoped()
    {
        return ($this->scope !== null);
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return $this
     */
    public function call()
    {
        $this->data = $this->getResource()->{$this->resolveMethod()}(...$this->resolveArguments());

        return $this;
    }

    /**
     * @return array
     */
    public function ancestors()
    {
        return $this->pipeline->before($this);
    }

    /**
     * @return array
     */
    public function descendants()
    {
        return $this->pipeline->after($this);
    }

    /**
     * @return bool
     */
    public function isLast()
    {
        return ($this->pipeline->last() === $this);
    }

    /**
     * @return array
     */
    protected function resolveArguments()
    {
        $arguments = [$this];

        if ($this->isLast()) {
            $arguments[] = $this->request;
        }

        return $arguments;
    }

    /**
     * @return string
     */
    protected function resolveMethod()
    {
        if (!$this->isLast()) {
            return 'getByKey';
        }

        switch ($this->request->getMethod()) {
            case 'POST':
                return 'create';
            case 'PUT';
                return 'update';
            case 'DELETE';
                return 'destroy';
            default:
                return 'get' . ($this->key ? 'Record' : 'Collection');
        }
    }
}
