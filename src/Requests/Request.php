<?php

namespace Api\Requests;

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
     * @var Parser
     */
    protected $parser;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->parser = new Parser();
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
            $path = strpos($_SERVER['REQUEST_URI'], '?') !== false ? substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];

            $this->segments = $this->parser->segments($path);
        }

        return $this->segments;
    }

    /**
     * @return array
     */
    public function relations()
    {
        if ($this->relations === null) {
            $this->relations = $this->parser->relations($_GET[Request::getRelationsKey()] ?? '');
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
            $this->filters = $this->parser->filters($_GET[Request::getFiltersKey()] ?? '');
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
            $this->sort = $this->parser->sort($_GET[Request::getSortKey()] ?? '');
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
