<?php

namespace Api\Requests;

/**
 * Class Config
 * @package Api\Requests
 */
class Config
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
}
