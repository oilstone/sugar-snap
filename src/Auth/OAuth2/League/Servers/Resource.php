<?php

namespace Api\Auth\OAuth2\League\Servers;

use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Api\Requests\Request;

class Resource
{
    protected $baseServer;

    public function __construct(ResourceServer $baseServer)
    {
        $this->baseServer = $baseServer;
    }

    public function validate(Request $request)
    {
        try {
            $this->baseServer->validateAuthenticatedRequest($request->getPsr7ServerRequest());
        } catch (OAuthServerException $exception) {
            $response->setPsr7Response(
                $exception->generateHttpResponse($response->getPsr7Response())
            )->emit();
        } catch (Exception $exception) {
            $response->write($exception->getMessage())->withStatus(500)->emit();
        }
    }
}