<?php

namespace Api\Requests;

use Api\Config\Config;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    protected static $config;

    /**
     * @return Config
     */
    public static function config(): Config
    {
        if (!static::$config)
        {
            return (new Config('request'))->accepts(
                'relationsKey',
                'filtersKey',
                'sortKey'
            )
                ->relationsKey('include')
                ->filtersKey('filter')
                ->sortKey('sort');
        }

        return static::$config;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function request()
    {
        $request = static::psr7ServerRequest();
        $segments = Parser::segments($request->getUri()->getPath());
        $relations = Parser::relations($request->getQueryParams()[static::$config->get('RelationsKey')] ?? '');
        $filters = Parser::filters($request->getQueryParams()[static::$config->get('FiltersKey')] ?? '');
        $sort = Parser::sort($request->getQueryParams()[static::$config->get('SortKey')] ?? '');

        return $request->withAttribute('segments', $segments)
            ->withAttribute('relations', $relations)
            ->withAttribute('filters', $filters)
            ->withAttribute('sort', $sort);
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function psr7ServerRequest()
    {
        $psr17Factory = new Psr17Factory();
        return (new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->fromGlobals();
    }
}
