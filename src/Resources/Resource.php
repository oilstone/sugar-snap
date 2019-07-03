<?php

namespace Api\Resources;

use Api\Api;
use Api\Pipeline\Pipe;
use Api\Pipeline\Pipeline;
use Api\Pipeline\Scope;
use Api\Requests\Request;
use Api\Resources\Relations\Collection as Relations;
use Api\Resources\Relations\HasMany;
use Api\Resources\Relations\Relation;
use Closure;

/**
 * Class Resource
 * @package Api\Resources
 */
class Resource
{
    /**
     * @var
     */
    protected $repository;

    /**
     * @var
     */
    protected $name;

    /**
     * @var Relations
     */
    protected $relations;

    /**
     * Resource constructor.
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
        $this->relations = new Relations();
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function hasMany(...$arguments)
    {
        $this->addRelation(array_merge([HasMany::class], $arguments));

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function nest(...$arguments)
    {
        $this->addRelation(array_merge([Relation::class], $arguments));

        return $this;
    }

    /**
     * @param $arguments
     * @return $this
     */
    protected function addRelation($arguments)
    {
        $class = array_shift($arguments);
        $name = array_shift($arguments);

        $this->relations->register(
            $name,
            function () use ($class, $name, $arguments) {
                /** @var Relation $relation */
                $relation = new $class($this);

                if (count($arguments) && $arguments[0] instanceof Closure) {
                    $arguments[0]($relation);
                }

                if (!$relation->getBinding()) {
                    $relation->bind($name);
                }

                return $relation->boot();
            }
        );

        return $this;
    }

    /**
     * @param string $name
     * @return Relation|null
     */
    public function getRelation(string $name)
    {
        return $this->relations->get($name);
    }

    /**
     * @param Pipe $pipe
     * @return mixed
     */
    public function getByKey(Pipe $pipe)
    {
        return $this->repository->getByKey($pipe);
    }

    /**
     * @param Pipe $pipe
     * @param Request $request
     * @return mixed
     */
    public function getCollection(Pipe $pipe, Request $request)
    {
        return Api::getRepresentation()->forCollection($request, $this->repository->getCollection($pipe, $request));
    }

    /**
     * @param Pipe $pipe
     * @param Request $request
     * @return mixed
     */
    public function getRecord(Pipe $pipe, Request $request)
    {
        return Api::getRepresentation()->forSingleton($request, $this->repository->getRecord($pipe, $request));
    }
}