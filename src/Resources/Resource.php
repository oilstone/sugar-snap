<?php

namespace Api\Resources;

use Api\Api;
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
     * @param int $id
     * @return mixed
     */
    public function getByKey(int $id)
    {
        return $this->repository->getByKey($id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getScopedByKey(int $id, Scope $scope)
    {
        return $this->repository->getScopedByKey($id, $scope);
    }

    /**
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getCollection(Request $request, Pipeline $pipeline)
    {
        return Api::getRepresentation()->forCollection($request, $this->repository->getCollection($request, $pipeline));
    }

    /**
     * @param Scope $scope
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getScopedCollection(Scope $scope, Request $request, Pipeline $pipeline)
    {
        return Api::getRepresentation()->forCollection($request, $this->repository->getScopedCollection($scope, $request, $pipeline));
    }

    /**
     * @param $key
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getRecord($key, Request $request, Pipeline $pipeline)
    {
        return Api::getRepresentation()->forSingleton($request, $this->repository->getRecord($key, $request, $pipeline));
    }

    /**
     * @param $key
     * @param Scope $scope
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getScopedRecord($key, Scope $scope, Request $request, Pipeline $pipeline)
    {
        return Api::getRepresentation()->forSingleton($request, $this->repository->getScopedRecord($key, $scope, $request, $pipeline));
    }
}