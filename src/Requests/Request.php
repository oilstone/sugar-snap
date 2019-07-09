<?php

namespace Api\Requests;

use Psr\Http\Message\ServerRequestInterface;
use Oilstone\RsqlParser\Expression;

/**
 * Class Request
 * @package Api\Requests
 */
class Request
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

    protected $serverRequest;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var
     */
    protected $segments;

    /**
     * @var
     */
    protected $filters;

    /**
     * @var
     */
    protected $relations;

    /**
     * @var
     */
    protected $sort;

    /**
     * Request constructor.
     * @param ServerRequestInterface $serverRequest
     * @param Parser $parser
     */
    public function __construct(ServerRequestInterface $serverRequest, Parser $parser)
    {
        $this->serverRequest = $serverRequest;
        $this->parser = $parser;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest()
    {
        return $this->serverRequest;
    }

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
     * @return array
     */
    public function segments()
    {
        if ($this->segments === null) {
            $this->segments = $this->parser->segments($this->serverRequest->getUri()->getPath());
        }

        return $this->segments;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * @return array
     */
    public function relations()
    {
        if ($this->relations === null) {
            $this->relations = $this->parser->relations(
                $this->serverRequest->getQueryParams()[Request::getRelationsKey()] ?? ''
            );
        }

        return $this->relations;
    }

    /**
     * @return mixed
     */
    public static function getRelationsKey()
    {
        return static::$keys['relations'];
    }

    /**
     * @return Expression
     */
    public function filters()
    {
        if ($this->filters === null) {
            $this->filters = $this->parser->filters(
                $this->serverRequest->getQueryParams()[Request::getFiltersKey()] ?? ''
            );
        }

        return $this->filters;
    }

    /**
     * @return mixed
     */
    public static function getFiltersKey()
    {
        return static::$keys['filters'];
    }

    /**
     * @return array
     */
    public function sort()
    {
        if ($this->sort === null) {
            $this->sort = $this->parser->sort(
                $this->serverRequest->getQueryParams()[Request::getSortKey()] ?? ''
            );
        }

        return $this->sort;
    }

    /**
     * @return string
     */
    public static function getSortKey()
    {
        return static::$keys['sort'];
    }
}
