<?php

namespace Api\Http\Requests;

class Provider
{
    public function register()
    {
        $this->$api->addConfig(
            (new Config('request'))->accepts(
                'relationsKey',
                'filtersKey',
                'sortKey',
                'limitKey'
            )
        );

    }
}