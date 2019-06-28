<?php

namespace Api\Requests;

use Oilstone\RsqlParser\Expression;
use Oilstone\RsqlParser\Parser as RsqlParser;

/**
 * Class Parser
 * @package Api\Requests
 */
class Parser
{
    /**
     * @param string $input
     * @return array
     */
    public function segments(string $input)
    {
        return array_filter(explode('/', $input));
    }

    /**
     * @param string $input
     * @return Expression
     */
    public function filters(string $input)
    {
        return RsqlParser::parse($input);
    }

    /**
     * @param string $input
     * @return array
     */
    public function relations(string $input)
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
    public function sort(string $input)
    {
        $sort = [];
        $items = array_filter(explode(',', $input));

        foreach ($items as $item) {
            $sort[] = Order::parse($item);
        }

        return $sort;
    }
}