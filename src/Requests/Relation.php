<?php

namespace Api\Requests;

use Oilstone\RsqlParser\Exceptions\InvalidQueryStringException;
use Oilstone\RsqlParser\Expression;
use Oilstone\RsqlParser\Parser as RsqlParser;

/**
 * Class Relation
 * @package Api\Requests
 */
class Relation
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $filters;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @param string $input
     * @return Relation
     * @throws InvalidQueryStringException
     */
    public static function parse(string $input)
    {
        $hierarchy = explode('.', $input, 2);
        $root = array_shift($hierarchy);
        $pieces = explode('[', rtrim($root, ']'));

        $instance = (new static())->setName($pieces[0])
            ->setFilters(RsqlParser::parse(count($pieces) > 1 ? $pieces[1] : ''));

        if (count($hierarchy)) {
            $instance->addRelation(Relation::parse($hierarchy[0]));
        }

        return $instance;
    }

    /**
     * @param Relation $relation
     */
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Expression
     */
    public function getFilters(): Expression
    {
        return $this->filters;
    }

    /**
     * @param Expression $filters
     * @return $this
     */
    public function setFilters(Expression $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param array $relations
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;
    }

    /**
     * @return bool
     */
    public function hasRelations()
    {
        return count($this->relations) > 0;
    }
}