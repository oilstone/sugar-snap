<?php

namespace Api\Auth\OAuth2\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client extends Entity implements ClientEntityInterface
{
    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getAttribute('id');
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string|string[]
     */
    public function getRedirectUri()
    {
        return $this->getAttribute('redirect_uri');
    }
}