<?php

namespace Api\Repositories\Stitch;

use Api\Guards\OAuth2\Sentinel;
use Api\Pipeline\Pipe;
use Api\Repositories\Contracts\Repository as RepositoryContract;
use Api\Resources\Resource;
use Exception;
use Oilstone\RsqlParser\Condition;
use Oilstone\RsqlParser\Expression;
use Stitch\Model;
use Stitch\Queries\Query;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Repository
 * @package Api\Repositories\Stitch
 */
class Repository implements RepositoryContract
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
     * @return array
     */
    public function getByKey(Pipe $pipe): array
    {
        return $this->keyedQuery($pipe)->first()->toArray();
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function getCollection(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array
    {
        $relations = $request->getAttribute('relations');

        $this->addRelations($pipe->getResource(), $relations);

        $query = $this->scopedQuery($pipe);

        $this->includeRelations($query, $relations)
            ->applyRsqlExpression($query, $request->getAttribute('filters'))
            ->applySorting($query, $request->getAttribute('sort'));

        return $query->get()->toArray();
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function getRecord(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array
    {
        $relations = $request->getAttribute('relations');

        $this->addRelations($pipe->getResource(), $relations);

        $query =  $this->keyedQuery($pipe);

        $this->includeRelations($query, $relations)
            ->applyRsqlExpression($query, $request->getAttribute('filters'))
            ->applySorting($query, $request->getAttribute('sort'));

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
     * @param Resource $resource
     * @param array $relations
     * @return $this
     */
    public function addRelations(Resource $resource, array $relations)
    {
        foreach ($relations as $requestRelation) {
            $name = $requestRelation->getName();
            $relation = $resource->getRelation($name);
            $foreignResource = $relation->getForeignResource();

            $this->model->addRelation($relation->make());

            $foreignResource->getRepository()->addRelations(
                $foreignResource,
                $requestRelation->getRelations()
            );
        }

        return $this;
    }

    /**
     * @param Query $query
     * @param array $relations
     * @return $this
     */
    public function includeRelations(Query $query, array $relations)
    {
        foreach ($relations as $relation) {
            $query->with($relation->path());

            $this->includeRelations($query, $relation->getRelations());
        }

        return $this;
    }

    /**
     * @param Query $query
     * @param array $orders
     * @return $this
     */
    protected function applySorting(Query $query, array $orders)
    {
        foreach ($orders as $order) {
            $query->orderBy($order->getProperty(), $order->getDirection());
        }

        return $this;
    }

    /**
     * @param $query
     * @param Expression $expression
     * @return $this
     */
    protected function applyRsqlExpression($query, Expression $expression)
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
                /** @var Condition $constraint */



                $query->{$method}($constraint->getColumn(), $constraint->getOperator()->toSql(), $constraint->getValue());
            }
        }

        return $this;
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     * @throws Exception
     */
    public function create(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array
    {
        throw new Exception('Method not yet implemented');
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     * @throws Exception
     */
    public function update(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array
    {
        throw new Exception('Method not yet implemented');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array
    {
        throw new Exception('Method not yet implemented');
    }
}
