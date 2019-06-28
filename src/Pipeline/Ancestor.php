<?php

namespace Api\Pipeline;

/**
 * Class Ancestor
 * @package Api\Pipeline
 */
class Ancestor
{
    /**
     * @var
     */
    protected $resource;

    /**
     * @var
     */
    protected $data;

    /**
     * Ancestor constructor.
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}