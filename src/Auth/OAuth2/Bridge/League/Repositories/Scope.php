<?php

namespace Api\Auth\OAuth2\Bridge\League\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Api\Auth\OAuth2\Bridge\League\Entities\Scope as Entity;
use Stitch\Stitch;
use Stitch\Model;


class Scope extends Repository implements ScopeRepositoryInterface
{
    protected $model;

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

    }

    /**
     * @return Model
     */
    protected function makeModel(): Model
    {
        return Stitch::make(function ($table)
        {
            $table->name('oauth_clients');
            $table->string('id')->primary();
            $table->string('name');
            $table->string('secret');
        })->hasMany('redirects', Stitch::make(function ($table)
        {
            $table->name('oauth_client_redirects');
            $table->integer('id')->autoIncrement()->primary();
            $table->string('client_id')->references('id')->on('oauth_clients');
            $table->string('uri');
        }));
    }
}