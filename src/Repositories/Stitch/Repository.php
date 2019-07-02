<?php

namespace Api\Repositories\Stitch;

use Api\Pipeline\Pipeline;
use Api\Pipeline\Scope;
use Api\Requests\Relation as RequestRelation;
use Api\Requests\Request;
use Api\Resources\Relations\Relation as ResourceRelation;
use Api\Resources\Resource;
use Oilstone\RsqlParser\Expression;
use Stitch\Model;
use Stitch\Queries\Query;

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
        return $this->applyKey($this->model->query(), $key)
            ->get()[0]
            ->toArray();
    }

    /**
     * @param $key
     * @param Scope $scope
     * @return mixed
     */
    public function getScopedByKey($key, Scope $scope)
    {
        return $this->applyScope(
            $this->applyKey($this->model->query(), $key),
            $scope
        )->get()[0]
            ->toArray();
    }

    /**
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function getCollection(Request $request, Pipeline $pipeline)
    {
        $query = $this->model->query();

        $this->addRelations($pipeline->current()->getResource(), $query, $request->relations());
        $this->applyRsqlExpression($query, $request->filters());

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
        $query = $this->applyScope($this->model->query(), $scope);

        $this->addRelations($pipeline->current()->getResource(), $query, $request->relations());
        $this->applyRsqlExpression($query, $request->filters());

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
        $query =  $this->applyKey($this->model->query(), $key);

        $this->addRelations($pipeline->current()->getResource(), $query, $request->relations());

        return $query->get()[0]->toArray();
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
        $query = $this->applyScope(
            $this->applyKey($this->model->query(), $key),
            $scope
        );

        $this->addRelations($pipeline->current()->getResource(), $query, $request->relations());

        return $query->get()[0]->toArray();
    }

    /**
     * @param Query $query
     * @param Scope $scope
     * @return Query
     */
    protected function applyScope(Query $query, Scope $scope)
    {
        return $query->where($scope->getKey(), $scope->getValue());
    }

    /**
     * @param Query $query
     * @param $key
     * @return Query
     */
    protected function applyKey(Query $query, $key)
    {
        return $query->where($this->model->getTable()->getPrimaryKey()->getName(), $key);
    }

    /**
     * @param $query
     * @param Expression $expression
     */
    function applyRsqlExpression($query, Expression $expression)
    {
        foreach ($expression as $item) {
            $method = $item['operator'] == 'OR' ? 'orWhere' : 'where';
            $constraint = $item['constraint'];

            if ($constraint instanceof Expression) {
                $query->{$method}(function ($query) use ($constraint)
                {
                    $this->applyRsqlExpression($query, $constraint);
                });
            } else {
                $query->{$method}($constraint->getColumn(), $constraint->getOperator()->toSql(), $constraint->getValue());
            }
        }
    }

    /**
     * @param Resource $resource
     * @param Query $query
     * @param array $relations
     */
    protected function addRelations(Resource $resource, Query $query, array $relations)
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
