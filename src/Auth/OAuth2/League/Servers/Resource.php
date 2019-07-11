<?php

namespace Api\Auth\OAuth2\League\Servers;

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
            return $this->baseServer->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            throw (new AuthException())->setBaseException($exception);
        }
    }
}