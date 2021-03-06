<?php

namespace Api\Specs\JsonApi;

use Api\Config\Service;

class Factory
{
    protected $config;

    /**
     * Factory constructor.
     * @param Service $config
     */
    public function __construct(Service $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public static function config()
    {
        return (new Service())->accepts(
            'relationsKey',
            'filtersKey',
            'sortKey'
        )
            ->relationsKey('include')
            ->filtersKey('filter')
            ->sortKey('sort');
    }

    /**
     * @return Representation
     */
    public function representation()
    {
        return new Representation();
    }
}
