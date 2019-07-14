<?php

namespace Api\Guards\OAuth2\Scopes;

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
     * @param string $operation
     * @param string $resource
     * @return bool
     */
    public function can(string $operation, string $resource)
    {
        foreach ($this->items as $scope) {
            if ($scope->isFor($resource) && $scope->isAllowedTo($operation)) {
                return true;
            }
        }

        return false;
    }
}