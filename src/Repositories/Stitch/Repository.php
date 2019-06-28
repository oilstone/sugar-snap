<?php

namespace Api\Repositories\Stitch;

use Stitch\Model;

class Repository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function find(int $id)
    {
        return $this->model->where('id', $id)->get()->toArray();
    }

    public function get($request, $pipeline)
    {
        $resource = $pipeline->current();
        $query = $this->model->query();

        $this->addRelations($resource, $query, $request->relations());

//        foreach ($request->filters() as $constraint) {
//            $query->where($constraint->getColumn(), $constraint->getOperator(), $constraint->getValue());
//        }

        return $query->get()->toArray();
    }

    public function expand($resourceRelation, $requestRelation)
    {
        $query = $resourceRelation->query();
        $resource = $resourceRelation->getForeignResource();
        $relations = $requestRelation->getRelations();

        $this->addRelations($resource, $query, $relations);

        return $query;
    }

    protected function addRelations($resource, $query, array $relations)
    {
        foreach ($relations as $requestRelation) {
            $name = $requestRelation->getName();
            $relation = $resource->getRelation($name);

            $query->addRelation(
                $name,
                $relation->getForeignResource()->getRepository()->expand($relation, $requestRelation)
            );
        }
    }
}
