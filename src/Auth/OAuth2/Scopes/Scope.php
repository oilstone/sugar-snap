<?php

namespace Api\Auth\OAuth2\Scopes;

class Scope
{
    protected $action;

    protected $resource;

    /**
     * @param string $scope
     * @return Scope
     */
    public static function parse(string $scope)
    {
        $pieces = explode(':', $scope);

        return (new static())->resource($pieces[0])->action($pieces[1]);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function action(string $name)
    {
        $this->action = $name;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAction(string $name)
    {
        return ($this->action === $name || $this->action === '*');
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
}