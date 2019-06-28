<?php

namespace Api\Requests;

class Request
{
    protected static $keys = [
        'relations' => 'include',
        'filters' => 'filter',
        'sort' => 'sort',
        'limit' => 'limit',
    ];

    protected $segments;

    protected $filters;

    protected $relations;

    protected $sort;

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
    public static function getRelationsKey()
    {
        return static::$keys['relations'];
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
    public static function getFiltersKey()
    {
        return static::$keys['filters'];
    }

    /**
     * @param string $key
     */
    public static function setSortKey(string $key)
    {
        static::$keys['sort'] = $key;
    }

    /**
     * @return string
     */
    public static function getsortKey()
    {
        return static::$keys['sort'];
    }

    public static function setLimitKey(string $key)
    {
        static::$keys['limit'] = $key;
    }

    public static function getLimitKey()
    {
        return static::$keys['limit'];
    }

    public function segments()
    {
        if ($this->segments === null) {
            $this->segments = $this->parser->segments($_SERVER['PHP_SELF']);
        }

        return $this->segments;
    }

    public function relations()
    {
        if ($this->relations === null) {
            $this->relations = $this->parser->relations($_GET[Request::getRelationsKey()] ?? '');
        }

        return $this->relations;
    }

    public function filters()
    {
        if ($this->filters === null) {
            $this->filters = $this->parser->filters($_GET[Request::getFiltersKey()] ?? '');
        }

        return $this->filters;
    }

    public function sort()
    {
        if ($this->sort === null) {
            $this->sort = $this->parser->sort($_GET[Request::getSortKey()] ?? '');
        }

        return $this->sort;
    }
}
