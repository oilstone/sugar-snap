<?php

namespace Api\Requests;

use Oilstone\RsqlParser\Parser as RsqlParser;
use Oilstone\RsqlParser\Expression;

class Relation
{
    protected $name;

    protected $filters;

    protected $relations = [];

    public static function parse(string $input)
    {
        $hierarchy = explode('.', $input,2);
        $root = array_shift($hierarchy);
        $pieces = explode('[' , rtrim($root, ']'));

        $instance = (new static())->setName($pieces[0])
            ->setFilters(RsqlParser::parse(count($pieces) > 1 ? $pieces[1] : ''));

        if (count($hierarchy)) {
            $instance->addRelation(Relation::parse($hierarchy[0]));
        }

        return $instance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getFilters(): Expression
    {
        return $this->filters;
    }

    public function setFilters(Expression $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    public function getRelations()
    {
        return $this->relations;
    }

    public function setRelations(array $relations)
    {
        $this->relations = $relations;
    }

    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    public function hasRelations()
    {
        return count($this->relations) > 0;
    }
}