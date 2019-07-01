<?php

namespace Api\Repositories\Stitch;

use Api\Pipeline\Pipeline;
use Api\Pipeline\Scope;
use Api\Requests\Relation as RequestRelation;
use Api\Requests\Request;
use Api\Resources\Relations\Relation as ResourceRelation;
use Api\Resources\Resource;
use Stitch\Model;

/**
 * Class Repository
 * @package Api\Repositories\Stitch
 */
class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Repository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getByKey($key)
    {
        return $this->model->query()
            ->where('id', $key)
            ->get()[0]
            ->toArray();
    }

    /**
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function getCollection(Request $request, Pipeline $pipeline)
    {
        $query = $this->model->query()
            ->get()
            ->toArray();

        $this->addRelations($pipeline->current(), $query, $request->relations());

        return $query->get()->toArray();
    }

    /**
     * @param Scope $scope
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function getScopedCollection(Scope $scope, Request $request, Pipeline $pipeline)
    {
        $query = $this->model->query()
            ->where($scope->getKey(), $scope->getValue())
            ->get()
            ->toArray();

        $this->addRelations($pipeline->current(), $query, $request->relations());

        return $query->get()->toArray();
    }

    /**
     * @param $key
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getRecord($key, Request $request, Pipeline $pipeline)
    {
        $query = $this->model->query()
            ->where('id', $key)
            ->get()[0]
            ->toArray();

        $this->addRelations($pipeline->current(), $query, $request->relations());

        return $query->get()->toArray();
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
        $query = $this->model->query()
            ->where('id', $key)
            ->where($scope->getKey(), $scope->getValue())
            ->get()[0]
            ->toArray();

        $this->addRelations($pipeline->current(), $query, $request->relations());

        return $query->get()->toArray();
    }

    /**
     * @param Resource $resource
     * @param $query
     * @param array $relations
     */
    protected function addRelations(Resource $resource, $query, array $relations)
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

    /**
     * @param $resourceRelation
     * @param $requestRelation
     * @return mixed
     */
    public function expand(ResourceRelation $resourceRelation, RequestRelation $requestRelation)
    {
        /** @noinspection PhpUndefinedMethodInspection */

        $query = $resourceRelation->query();

        $this->addRelations($resourceRelation->getForeignResource(), $query, $requestRelation->getRelations());

        return $query;
    }
}
