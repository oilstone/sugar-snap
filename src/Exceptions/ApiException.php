<?php

namespace Api\Exceptions;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ApiException extends RuntimeException
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
    public function formatResponse(ResponseInterface $response)
    {
        $response->getBody()->write(
            $this->buildPayload()->toJson()
        );

        return $response->withStatus(500);
    }
}