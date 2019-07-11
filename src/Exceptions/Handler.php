<?php

namespace Api\Exceptions;

use Exception;

class Handler
{
    protected $exception;

    /**
     * Handler constructor.
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    public function respond($response, $emitter): void
    {
        if ($this->exception instanceof ApiException) {
            $emitter->emit($this->exception->formatResponse($response));
        } else {
            $response->getBody()->write(
                (new Payload())->message($this->exception->getMessage())->toJson()
            );

            $emitter->emit($response->withStatus(500));
        }
    }
}
