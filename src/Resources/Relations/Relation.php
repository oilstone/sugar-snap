<?php

namespace Api\Resources\Relations;

use Api\Factory;
use Api\Registry;
use Api\Resources\Resource;
use Exception;

/**
 * Class Relation
 * @package Api\Resources\Relations
 */
class Relation
{
    protected $factory;

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
     * @var string|null
     */
    protected $foreignKey;

    /**
     * @var string|null
     */
    protected $localKey;

    /**
     * Relation constructor.
     * @param Factory $factory
     * @param Resource $localResource
     */
    public function __construct(Factory $factory, Resource $localResource)
    {
        $this->factory = $factory;
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
            $this->foreignResource = $this->factory->resource()->registry()->get($this->binding);

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

    /**
     * @param string $name
     * @return $this
     */
    public function foreignKey(string $name)
    {
        $this->foreignKey = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function localKey(string $name)
    {
        $this->localKey = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @return bool
     */
    public function hasKeys()
    {
        return ($this->foreignKey && $this->foreignKey);
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function boot()
    {
        if (!$this->hasKeys()) {
            if (method_exists($this, 'pullKeys')) {
                $this->pullKeys();

                return $this;
            }
        }

        return $this;
    }
}