<?php

namespace Api\Auth\OAuth2\Scopes;

class Collection
{
    protected $items = [];

    /**
     * @param Scope $scope
     * @return $this
     */
    public function push(Scope $scope)
    {
        $this->items[] = $scope;

        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function fill(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->push($scope);
        }

        return $this;
    }

    /**
     * @param string $action
     * @param string $resource
     * @return bool
     */
    public function can(string $action, string $resource)
    {
        foreach ($this->items as $item) {
            if ($item->isFor($resource) && $item->hasAction($action)) {
                return true;
            }
        }

        return false;
    }
}