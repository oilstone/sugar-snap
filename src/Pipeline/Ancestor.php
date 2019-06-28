<?php

namespace Api\Pipeline;

class Ancestor
{
    protected $resource;

    protected $data;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}