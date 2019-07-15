<?php

namespace Api\Guards\OAuth2\Scopes;

class Scope
{
    protected $operation;

    protected $resource;

    /**
     * @param string $scope
     * @return Scope
     */
    public static function parse(string $scope)
    {
        $pieces = explode(':', $scope);

        return (new static())->resource($pieces[0])->operation($pieces[1]);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function operation(string $name)
    {
        $this->operation = $name;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isAllowedTo(string $name)
    {
        return ($this->operation === $name || $this->operation === '*');
    }

    /**
     * @param string $name
     * @return $this
     */
    public function resource(string $name)
    {
        $this->resource = $name;

        return $this;
    }

    /**
     * @param string $resource
     * @return bool
     */
    public function isFor(string $resource)
    {
        return ($this->resource === $resource);
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return$this->operation;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }
}