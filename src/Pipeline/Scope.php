<?php

namespace Api\Pipeline;

use Api\Resources\Relations\Relation;

class Scope
{
    protected $ancestor;

    protected $relation;

    public function __construct(Pipe $ancestor, Relation $relation)
    {
        $this->ancestor = $ancestor;
        $this->relation = $relation;
    }

    public function getKey()
    {
        return $this->relation->getForeignKey();
    }

    public function getValue()
    {
        return $this->ancestor->getData()[$this->getKey()] ?? null;
    }
}
