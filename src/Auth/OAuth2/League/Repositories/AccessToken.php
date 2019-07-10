<?php

namespace Api\Auth\OAuth2\League\Repositories;

use Api\Auth\OAuth2\League\Entities\AccessToken as Entity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Stitch\Model;

class AccessToken implements AccessTokenRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

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
        $record = $this->model->make([
            'id' => $accessTokenEntity->getIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'revoked' => false,
            'expires_at' => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s')
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
        $record = $this->model->find($tokenId);
        $record->revoked = true;
        $record->save();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->model->find($tokenId)->revoked;
    }
}
