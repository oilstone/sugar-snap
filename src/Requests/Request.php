<?php

namespace Api\Requests;

use Psr\Http\Message\ServerRequestInterface as Psr7ServerRequestInterface;
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

    protected $psr7ServerRequest;

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
     * @param Psr7ServerRequestInterface $psr7ServerRequest
     * @param Parser $parser
     */
    public function __construct(Psr7ServerRequestInterface $psr7ServerRequest, Parser $parser)
    {
        $this->psr7ServerRequest = $psr7ServerRequest;
        $this->parser = $parser;
    }

    /**
     * @return Psr7ServerRequestInterface
     */
    public function getPsr7ServerRequest()
    {
        return $this->psr7ServerRequest;
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
            $this->segments = $this->parser->segments($this->psr7ServerRequest->getUri()->getPath());
        }

        return $this->segments;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->psr7ServerRequest->getMethod();
    }

    /**
     * @return array
     */
    public function relations()
    {
        if ($this->relations === null) {
            $this->relations = $this->parser->relations(
                $this->psr7ServerRequest->getQueryParams()[Request::getRelationsKey()] ?? ''
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
                $this->psr7ServerRequest->getQueryParams()[Request::getFiltersKey()] ?? ''
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
                $this->psr7ServerRequest->getQueryParams()[Request::getSortKey()] ?? ''
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
