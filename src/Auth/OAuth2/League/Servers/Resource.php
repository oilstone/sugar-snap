<?php

namespace Api\Auth\OAuth2\League\Servers;

use Api\Auth\OAuth2\Scopes\Scope;
use Api\Auth\OAuth2\Scopes\Collection as Scopes;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Api\Auth\OAuth2\League\Exceptions\AuthException;
use Psr\Http\Message\ServerRequestInterface;

class Resource
{
    protected $baseServer;

    public function __construct(ResourceServer $baseServer)
    {
        $this->baseServer = $baseServer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function validate(ServerRequestInterface $request): ServerRequestInterface
    {
        try {
            $request = $this->baseServer->validateAuthenticatedRequest($request);

            return $request->withAttribute('oauth_scopes', (new Scopes())->fill(array_map(function ($scope)
            {
                return Scope::parse($scope);
            }, $request->getAttribute('oauth_scopes'))));
        } catch (OAuthServerException $exception) {
            throw (new AuthException())->setBaseException($exception);
        }
    }
}