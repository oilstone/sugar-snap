<?php

namespace Api\Auth\OAuth2\Bridge\League\Entities;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    /**
     * Create a new scope instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->setIdentifier($name);
    }

    /**
     * Get the data that should be serialized to JSON.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
