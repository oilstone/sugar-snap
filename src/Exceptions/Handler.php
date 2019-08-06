<?php

namespace Api\Exceptions;

use Api\Exceptions\Contracts\ApiException as ApiExceptionInterface;
use Exception;

class Handler
{
    protected $response;

    /**
     * Handler constructor.
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param Exception $exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Exception $exception)
    {
        if ($exception instanceof ApiExceptionInterface) {
            return $exception->formatResponse($this->response);
        } else {
            return $this->response->getBody()->write(
                (new Payload())->message($exception->getMessage())->toJson()
            );
        }
    }
}
