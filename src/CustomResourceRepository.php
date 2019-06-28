<?php

namespace Api;


class CustomResourceRepository
{
    public function find($id)
    {
        return [
            'id' => 1,
            'prop' => 'value'
        ];
    }

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