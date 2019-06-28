<?php

namespace Api\Resources\Relations;

use Api\Registry;
use Api\Resources\Resource;

/**
 * Class Relation
 * @package Api\Resources\Relations
 */
class Relation
{
    /**
     * @var Resource
     */
    protected $localResource;

    /**
     * @var
     */
    protected $foreignResource;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $binding;

    /**
     * Relation constructor.
     * @param Resource $localResource
     */
    public function __construct(Resource $localResource)
    {
        $this->localResource = $localResource;
    }

    /**
     * @return Resource
     */
    public function getLocalResource()
    {
        return $this->localResource;
    }

    /**
     * @param Resource $model
     * @return $this
     */
    public function foreignResource(Resource $model)
    {
        $this->foreignResource = $model;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getForeignResource()
    {
        if ($this->foreignResource) {
            return $this->foreignResource;
        }

        if ($this->binding) {
            $this->foreignResource = Registry::get($this->binding);

            return $this->foreignResource;
        }

        return null;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function bind(string $name)
    {
        $this->binding = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBinding()
    {
        return $this->binding;
    }
}