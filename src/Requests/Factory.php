<?php

namespace Api\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    /**
     * @var array
     */
    protected static $keys = [
        'relations' => 'include',
        'filters' => 'filter',
        'sort' => 'sort',
        'limit' => 'limit',
    ];

    /**
     * @param string $key
     */
    public static function setRelationsKey(string $key)
    {
        static::$keys['relations'] = $key;
    }

    /**
     * @param string $key
     */
    public static function setFiltersKey(string $key)
    {
        static::$keys['filters'] = $key;
    }

    /**
     * @param string $key
     */
    public static function setSortKey(string $key)
    {
        static::$keys['sort'] = $key;
    }

    /**
     * @param string $key
     */
    public static function setLimitKey(string $key)
    {
        static::$keys['limit'] = $key;
    }

    /**
     * @return mixed
     */
    public static function getLimitKey()
    {
        return static::$keys['limit'];
    }

    /**
     * @return mixed
     */
    public static function getRelationsKey()
    {
        return static::$keys['relations'];
    }

    /**
     * @return mixed
     */
    public static function getFiltersKey()
    {
        return static::$keys['filters'];
    }

    /**
     * @return string
     */
    public static function getSortKey()
    {
        return static::$keys['sort'];
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function make()
    {
        $request = static::psr7ServerRequest();
        $parser = static::parser();
        $segments = $parser->segments($request->getUri()->getPath());
        $relations = $parser->relations($request->getQueryParams()[static::getRelationsKey()] ?? '');
        $filters = $parser->filters($request->getQueryParams()[static::getFiltersKey()] ?? '');
        $sort = $parser->sort($request->getQueryParams()[static::getSortKey()] ?? '');

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
