<?php

namespace Api\Repositories\Stitch;

use Api\Pipeline\Pipeline;
use Api\Requests\Relation as RequestRelation;
use Api\Requests\Request;
use Api\Resources\Relations\Relation;
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
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        /** @noinspection PhpUndefinedMethodInspection */

        return $this->model->where('id', $id)->get()->toArray();
    }

    public function getCollection(Request $request, Pipeline $pipeline)
    {

    }

    public function getScopedCollection()
    {

    }

    public function getResource()
    {

    }

    public function getScopedResource()
    {

    }

    /**
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function get(Request $request, Pipeline $pipeline)
    {
        $resource = $pipeline->current();
        $query = $this->model->query();

        $this->addRelations($resource, $query, $request->relations());

//        foreach ($request->filters() as $constraint) {
//            $query->where($constraint->getColumn(), $constraint->getOperator(), $constraint->getValue());
//        }

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
    public function expand(Relation $resourceRelation, RequestRelation $requestRelation)
    {
        /** @noinspection PhpUndefinedMethodInspection */

        $query = $resourceRelation->query();
        $resource = $resourceRelation->getForeignResource();
        $relations = $requestRelation->getRelations();

        $this->addRelations($resource, $query, $relations);

        return $query;
    }
}
