<?php

namespace Api\Guards\OAuth2;

use Api\Guards\OAuth2\Scopes\Collection as Scopes;

class Sentinel
{
    protected $server;

    protected $accessTokenId;

    protected $clientId;

    protected $userId;

    protected $scopes;

    /**
     * Sentinel constructor.
     * @param $server
     * @param $request
     */
    public function __construct($server, $request, $pipeline)
    {
        $this->server = $server;
        $this->request = $request;
        $this->pipeline = $pipeline;
    }

    /**
     * @param $pipeline
     * @return $this
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
            $this->extractOauthAttributes(
                $this->server->validateAuthenticatedRequest($this->request)
            );
        } catch (OAuthServerException $exception) {
            throw (new AuthException())->setBaseException($exception);
        }

        return $this;
    }

    /**
     * @param $request
     */
    protected function extractOauthAttributes($request)
    {
        $this->accessTokenId = $request->getAttribute('oauth_access_token_id');
        $this->clientId = $request->getAttribute('oauth_client_id');
        $this->userId = $request->getAttribute('oauth_user_id');

        $this->scopes = (new Scopes())->fill(array_map(function ($scope)
        {
            return Scope::parse($scope);
        }, $request->getAttribute('oauth_scopes')));
    }

    /**
     * @param $pipeline
     * @return $this
     */
    protected function checkPipeline()
    {
        foreach ($this->pipeline->all() as $pipe) {
            $this->verify($pipe->getOperation(), $pipe->getResource());
        }

        return $this;
    }

    /**
     * @param $requestRelations
     * @return $this
     */
    protected function checkRelations($resource, $requestRelations)
    {
        foreach ($requestRelations as $requestRelation) {
            $relation = $resource->getRelation($requestRelation->getName());

            if ($relation) {
                $this->verify('read', $relation);
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
     */
    public function verify(string $operation, $resource)
    {
        if (!$this->scopes->can($operation, $resource->getName())) {
            $this->reject();
        }
    }

    /**
     * @throws \Exception
     */
    protected function reject()
    {
        throw new \Exception('uh uh uh');
    }
}
