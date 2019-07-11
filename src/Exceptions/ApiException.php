<?php

namespace Api\Exceptions;

use Api\Exceptions\Contracts\ApiException as ApiExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ApiException extends RuntimeException implements ApiExceptionInterface
{
    /**
     * @return Payload
     */
    public function buildPayload()
    {
        return (new Payload())->message($this->message);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function formatResponse(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(
            $this->buildPayload()->toJson()
        );

        return $response->withStatus(500);
    }
}
