<?php

namespace Api\Http\Requests;

use Oilstone\RsqlParser\Expression;
use Oilstone\RsqlParser\Parser as RsqlParser;

/**
 * Class Parser
 * @package Api\Http\Requests
 */
class Parser
{
    /**
     * @param string $input
     * @return array
     */
    public static function segments(string $input)
    {
        return array_values(array_filter(explode('/', $input)));
    }

    /**
     * @param string $input
     * @return Expression
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function filters(string $input)
    {
        return RsqlParser::parse($input);
    }

    /**
     * @param string $input
     * @return array
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function relations(string $input)
    {
        $relations = [];
        $items = array_filter(explode(',', $input));

        foreach ($items as $item) {
            $relations[] = Relation::parse($item);
        }

        return $relations;
    }

    /**
     * @param string $input
     * @return array
     */
    public static function sort(string $input)
    {
        $sort = [];
        $items = array_filter(explode(',', $input));

        foreach ($items as $item) {
            $sort[] = Order::parse($item);
        }

        return $sort;
    }
}