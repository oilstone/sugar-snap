<?php

namespace Api\Auth\OAuth2\League\Servers;

use Api\Auth\OAuth2\League\Exceptions\AuthException;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Authorisation
{
    protected $baseServer;

    public function __construct(AuthorizationServer $baseServer)
    {
        $this->baseServer = $baseServer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function issueToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            return $this->baseServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            throw (new AuthException())->setBaseException($exception);
        }
    }
}
