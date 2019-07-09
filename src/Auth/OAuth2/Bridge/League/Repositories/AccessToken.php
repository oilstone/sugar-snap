<?php

namespace Api\Auth\OAuth2\Bridge\League\Repositories;

use Api\Auth\OAuth2\Bridge\League\Entities\AccessToken as Entity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Stitch\Model;

class AccessToken extends Repository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new Entity($clientEntity, $scopes, $userIdentifier);
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $record = $this->getModel()->make([
            'id' => $accessTokenEntity->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'revoked' => false,
            'expires_at' => $accessTokenEntity->getExpiryDateTime()
        ]);

        foreach ($accessTokenEntity->getScopes() as $scope) {
            $record->scopes->new([
                'name' => $scope->getIdentifier()
            ]);
        }

        $record->save()->scopes->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $record = $this->getModel()->find($tokenId);
        $record->revoked = true;
        $record->save();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->getModel()->find($tokenId)->revoked;
    }

    /**
     * @return Model
     */
    protected function makeModel(): Model
    {
        return Stitch::make(function ($table)
        {
            $table->name('oauth_access_tokens');
            $table->integer('id')->primary();
            $table->string('client_id');
            $table->integer('user_id')->primary();
            $table->boolean('revoked');
            $table->datetime('expires_at');
        })->hasMany('scopes', Stitch::make(function ($table)
        {
            $table->name('oauth_access_token_scopes');
            $table->integer('id')->autoIncrement()->primary();
            $table->integer('oauth_access_token_id')->references('id')->on('oauth_access_tokens');
            $table->string('name');
        }));
    }
}
