<?php

namespace Api\Pipeline;

class Scope
{
    public function __construct(Pipe $pipe, $ancestor)
    {

    }

    public function query()
    {
        return $this->relation->applyScope($this->ancestor);
    }

    public function get()
    {

    }
}