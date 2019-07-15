<?php

namespace Api\Guards\OAuth2\League\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Api\Guards\OAuth2\League\Entities\Scope as Entity;
use Stitch\Model;


class Scope implements ScopeRepositoryInterface
{
    /**
     * @param string $identifier
     * @return Entity
     */
    public function getScopeEntityByIdentifier($identifier): Entity
    {
        return new Entity($identifier);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string                 $grantType
     * @param ClientEntityInterface  $clientEntity
     * @param null|string            $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        return $scopes;
    }
}