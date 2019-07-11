<?php

namespace Api\Requests;

use Api\Config\Config;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    /**
     * @return Config
     */
    public static function config(): Config
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

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function request(Config $config)
    {
        $request = static::psr7ServerRequest();
        $parser = static::parser();
        $segments = $parser->segments($request->getUri()->getPath());
        $relations = $parser->relations($request->getQueryParams()[$config->get('RelationsKey')] ?? '');
        $filters = $parser->filters($request->getQueryParams()[$config->get('FiltersKey')] ?? '');
        $sort = $parser->sort($request->getQueryParams()[$config->get('SortKey')] ?? '');

        return $request->withAttribute('segments', $segments)
            ->withAttribute('relations', $relations)
            ->withAttribute('filters', $filters)
            ->withAttribute('sort', $sort);
    }

    /**
     * @return Parser
     */
    public static function parser()
    {
        return new Parser();
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
