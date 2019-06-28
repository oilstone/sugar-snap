<?php

namespace Api\Resources;

use Api\Resources\Relations\Collection as Relations;
use Api\Resources\Relations\Relation;
use Api\Resources\Relations\HasMany;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Schema\Arr as ArrSchema;
use Neomerx\JsonApi\Wrappers\Arr;
use Closure;

class Resource
{
    protected $repository;

    protected $name;

    protected $relations;

    public function __construct($repository)
    {
        $this->repository = $repository;
        $this->relations = new Relations();
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasMany(...$arguments)
    {
        $this->addRelation(array_merge([HasMany::class], $arguments));

        return $this;
    }

    public function nest(...$arguments)
    {
        $this->addRelation(array_merge([Relation::class], $arguments));

        return $this;
    }

    protected function addRelation($arguments)
    {
        $class = array_shift($arguments);
        $name = array_shift($arguments);

        $this->relations->register(
            $name,
            function () use ($class, $name, $arguments)
            {
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

    public function getRelation(string $name)
    {
        return $this->relations->get($name);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function get($request, $pipeline)
    {
        $encoder = Encoder::instance([
            Arr::class => ArrSchema::class
        ])->withEncodeOptions(JSON_PRETTY_PRINT)
            ->withIncludedPaths([
               'items.product-item-attributes'
            ]);


        var_dump($this->repository->get($request, $pipeline));
        exit;

        return $encoder->encodeCollectionArray(
            'products',
            $this->repository->get($request, $pipeline)
        );
    }
}