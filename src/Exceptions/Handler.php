<?php

namespace Api\Exceptions;

use Api\Exceptions\Contracts\ApiException as ApiExceptionInterface;
use Exception;

class Handler
{
    protected $response;

    protected $emitter;

    /**
     * Handler constructor.
     * @param $response
     * @param $emitter
     */
    public function __construct($response, $emitter)
    {
        $this->response = $response;
        $this->emitter = $emitter;
    }

    /**
     * @param Exception $exception
     */
    public function handle(Exception $exception): void
    {
        if ($exception instanceof ApiExceptionInterface) {
            $this->emitter->emit($exception->formatResponse($this->response));
        } else {
            $this->response->getBody()->write(
                (new Payload())->message($exception->getMessage())->toJson()
            );

            $this->emitter->emit($this->response->withStatus(500));
        }
    }
}
