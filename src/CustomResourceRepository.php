<?php

namespace Api;


/**
 * Class CustomResourceRepository
 * @package Api
 */
class CustomResourceRepository
{
    /**
     * @param $id
     * @return array
     */
    public function find($id)
    {
        return [
            'id' => 1,
            'prop' => 'value'
        ];
    }

    /**
     * @param $request
     * @param $pipeline
     * @return array
     */
    public function get($request, $pipeline)
    {
        return [
            [
                'id' => 1,
                'prop' => 'value'
            ],
            [
                'id' => 2,
                'prop' => 'value 2'
            ]
        ];
    }
}