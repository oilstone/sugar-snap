<?php

namespace Api\Repositories\Stitch;

use Api\Pipeline\Pipe;
use Api\Http\Requests\Relation as RequestRelation;
use Api\Resources\Relations\Relation as ResourceRelation;
use Api\Resources\Resource;
use Oilstone\RsqlParser\Expression;
use Stitch\Model;
use Stitch\Queries\Query;
use Psr\Http\Message\ServerRequestInterface;

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
     * @param Pipe $pipe
     * @return mixed
     */
    public function getByKey(Pipe $pipe)
    {
        return $this->keyedQuery($pipe)->first()->toArray();
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getCollection(Pipe $pipe, ServerRequestInterface $request)
    {
        $query = $this->scopedQuery($pipe);
        $this->addRelations($pipe->getResource(), $query, $request->getAttribute('relations'));
        $this->applyRsqlExpression($query, $request->getAttribute('filters'));

        return $query->get()->toArray();
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getRecord(Pipe $pipe, ServerRequestInterface $request)
    {
        $query =  $this->keyedQuery($pipe);
        $this->addRelations($pipe->getResource(), $query, $request->getAttribute('relations'));
        $this->applyRsqlExpression($query, $request->getAttribute('filters'));

        return $query->first()->toArray();
    }

    /**
     * @return Query
     */
    protected function query(): Query
    {
        return $this->model->query()->dehydrated();
    }

    /**
     * @param Pipe $pipe
     * @return Query
     */
    protected function scopedQuery(Pipe $pipe): Query
    {
        return $this->applyScope($this->query(), $pipe);
    }

    /**
     * @param Pipe $pipe
     * @return Query
     */
    protected function keyedQuery(Pipe $pipe): Query
    {
        return $this->applyKey($this->scopedQuery($pipe), $pipe);
    }

    /**
     * @param Query $query
     * @param Pipe $pipe
     * @return Query
     */
    protected function applyScope(Query $query, Pipe $pipe)
    {
        if ($pipe->isScoped()) {
            $scope = $pipe->getScope();

            $query->where($scope->getKey(), $scope->getValue());
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param Pipe $pipe
     * @return Query
     */
    protected function applyKey(Query $query, Pipe $pipe)
    {
        return $query->where($this->model->getTable()->getPrimaryKey()->getName(), $pipe->getKey());
    }

    /**
     * @param $query
     * @param Expression $expression
     */
    public function applyRsqlExpression($query, Expression $expression)
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
                $relation->getForeignResource()->getRepository()->include($relation, $requestRelation)
            );
        }
    }

    /**
     * @param $resourceRelation
     * @param $requestRelation
     * @return mixed
     */
    public function include(ResourceRelation $resourceRelation, RequestRelation $requestRelation)
    {
        /** @noinspection PhpUndefinedMethodInspection */

        $query = $resourceRelation->query();

        $this->addRelations($resourceRelation->getForeignResource(), $query, $requestRelation->getRelations());

        return $query;
    }
}
