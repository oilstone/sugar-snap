<?php

namespace Api\Guards\OAuth2;

use Api\Guards\OAuth2\League\Exceptions\AuthException;
use Api\Guards\OAuth2\Scopes\Collection as Scopes;
use Api\Guards\OAuth2\Scopes\Scope;
use Api\Pipeline\Pipeline;
use Api\Resources\Resource;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;

class Sentinel
{
    protected $server;

    protected $request;

    protected $pipeline;

    protected $accessTokenId;

    protected $clientId;

    protected $userId;

    protected $scopes;

    /**
     * Sentinel constructor.
     * @param ResourceServer $server
     * @param ServerRequestInterface $request
     * @param Pipeline $pipeline
     */
    public function __construct(ResourceServer $server, ServerRequestInterface $request, Pipeline $pipeline)
    {
        $this->server = $server;
        $this->request = $request;
        $this->pipeline = $pipeline;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function protect()
    {
        $this->checkToken()
            ->checkPipeline()
            ->checkRelations(
                $this->pipeline->last()->getResource(),
                $this->request->getAttribute('relations')
            );

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkToken()
    {
        try {
            return $this->extractOauthAttributes(
                $this->server->validateAuthenticatedRequest($this->request)
            );
        } catch (OAuthServerException $exception) {
            throw (new AuthException())->setBaseException($exception);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return $this
     */
    protected function extractOauthAttributes(ServerRequestInterface $request)
    {
        $this->accessTokenId = $request->getAttribute('oauth_access_token_id');
        $this->clientId = $request->getAttribute('oauth_client_id');
        $this->userId = $request->getAttribute('oauth_user_id');

        $this->scopes = (new Scopes())->fill(array_map(function ($scope)
        {
            return Scope::parse($scope);
        }, $request->getAttribute('oauth_scopes')));

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function checkPipeline()
    {
        foreach ($this->pipeline->all() as $pipe) {
            $this->verify($pipe->getOperation(), $pipe->getResource()->getName());
        }

        return $this;
    }

    /**
     * @param Resource $resource
     * @param array $requestRelations
     * @return $this
     * @throws \Exception
     */
    protected function checkRelations(Resource $resource, array $requestRelations)
    {
        foreach ($requestRelations as $requestRelation) {
            $relation = $resource->getRelation($requestRelation->getName());

            if ($relation) {
                $this->verify('read', $relation->getName());
                $this->checkRelations(
                    $relation->getForeignResource(),
                    $requestRelation->getRelations()
                );
            }
        }

        return $this;
    }

    /**
     * @param string $operation
     * @param string $resource
     * @throws \Exception
     */
    public function verify(string $operation, string $resource)
    {
        if ($this->scopes === null || !$this->scopes->can($operation, $resource)) {
            $this->reject();
        }
    }

    /**
     * @throws \Exception
     */
    protected function reject()
    {
        throw new \Exception('access_denied');
    }
}
