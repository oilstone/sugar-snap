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

    protected $action;

    protected $method;

    protected $arguments;

    protected const ACTION_MAP = [
        'POST' => 'create',
        'GET' => 'read',
        'PUT' => 'update',
        'DELETE' => 'delete'
    ];

    protected const METHOD_MAP = [
        'POST' => 'create',
        'PUT' => 'update',
        'DELETE' => 'destroy'
    ];

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
        $this->resolve();
        $resource = $this->getResource();

        if (!$this->request->getAttribute('oauth_scopes')->can($this->action, $resource->getName())) {
            throw new \Exception('you cant do that');
        }

        $this->data = $resource->{$this->method}(...$this->arguments);

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
     * @return $this
     */
    protected function resolve()
    {
        $httpMethod = $this->request->getMethod();
        $this->arguments = [$this];

        if ($this->isLast()) {
            $this->arguments[] = $this->request;
        } else {
            $this->action = 'read';
            $this->method = 'getByKey';

            return $this;
        }

        $this->action = $this::ACTION_MAP[$httpMethod];

        $this->method = $httpMethod === 'GET'
            ? 'get' . ($this->hasKey() ? 'Record' : 'Collection')
            : $this::METHOD_MAP[$httpMethod];

        return $this;
    }
}
