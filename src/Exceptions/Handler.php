<?php

namespace Api\Exceptions;

use Exception;

class Handler
{
    protected $exception;

    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    public function respond($response)
    {
        if () {
            $this->exception->respond($response);
        } else {
            $response->write($this->exception->getMessage())->withStatus(500)->emit();
        }
    }
}