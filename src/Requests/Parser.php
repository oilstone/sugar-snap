<?php

namespace Api\Requests;

use Oilstone\RsqlParser\Parser as RsqlParser;

class Parser
{
    /**
     * @param string $path
     * @return array
     */
    public function segments(string $input)
    {
        return array_filter(explode('/', $input));
    }

    /**
     * @param string $filters
     * @return \Oilstone\RsqlParser\Expression
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

        foreach($items as $item) {
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