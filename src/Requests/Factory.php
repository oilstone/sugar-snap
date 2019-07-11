<?php

namespace Api\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function make()
    {
        $request = static::psr7ServerRequest();
        $parser = static::parser();
        $segments = $parser->segments($request->getUri()->getPath());
        $relations = $parser->relations($request->getQueryParams()[Config::getRelationsKey()] ?? '');
        $filters = $parser->filters($request->getQueryParams()[Config::getFiltersKey()] ?? '');
        $sort = $parser->sort($request->getQueryParams()[Config::getSortKey()] ?? '');

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
