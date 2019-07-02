<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Pipeline\Scope;
use Api\Requests\Request;

/**
 * Class CustomResourceRepository
 * @package Api
 */
class CustomResourceRepository
{
    /**
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function getCollection(Request $request, Pipeline $pipeline)
    {
        return [
            'id' => 1,
            'name' => 'a custom resource record'
        ];
    }

    /**
     * @param Scope $scope
     * @param Request $request
     * @param Pipeline $pipeline
     * @return array
     */
    public function getScopedCollection(Scope $scope, Request $request, Pipeline $pipeline)
    {
        return [
            [
                'id' => 1,
                'name' => 'a custom resource record'
            ],
            [
                'id' => 2,
                'name' => 'a custom resource record'
            ]
        ];
    }

    /**
     * @param $key
     * @param Request $request
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getRecord($key, Request $request, Pipeline $pipeline)
    {
        return [
            'id' => 1,
            'name' => 'a custom resource record'
        ];
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
        var_dump($scope->getKey());
        var_dump($scope->getValue());

        return [
            'id' => 1,
            'name' => 'a custom resource record'
        ];
    }
}