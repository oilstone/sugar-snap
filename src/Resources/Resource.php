<?php

namespace Api\Resources;

use Api\Representations\Contracts\Representation;
use Api\Representations\JsonApi;
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
     * @var Representation
     */
    protected $representation;

    /**
     * Resource constructor.
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
        $this->relations = new Relations();
        $this->representation = new JsonApi();
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
                } else {
                    $relation->bind($name);
                }

                return $relation;
            }
        );

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
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $request
     * @param $pipeline
     * @return mixed
     */
    public function get($request, $pipeline)
    {
        return $this->representation->forCollection($request, $this->repository->get($request, $pipeline));
    }
}