<?php

namespace Api\Responses;

use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Psr\Http\Message\StreamInterface as Psr7StreamInterface;

class Response
{
    protected $psr7Response;

    protected $emitter;

    public function __construct(Psr7ResponseInterface $psr7Response, Psr7StreamInterface $stream, $emitter)
    {
        $this->psr7Response = $psr7Response->withBody($stream);
        $this->emitter = $emitter;
    }

    /**
     * @return Psr7ResponseInterface
     */
    public function getPsr7Response()
    {
        return $this->psr7Response;
    }

    /**
     * @param Psr7ResponseInterface $psr7Response
     * @return $this
     */
    public function setPsr7Response(Psr7ResponseInterface $psr7Response)
    {
        $this->psr7Response = $psr7Response;

        return $this;
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus(int $code, string $reasonPhrase = '')
    {
        $this->psr7Response = $this->psr7Response->withStatus($code, $reasonPhrase);

        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function write(string $string)
    {
        $this->psr7Response->getBody()->write($string);

        return $this;
    }

    public function emit(): void
    {
        $this->emitter->emit($this->psr7Response);
    }
}
