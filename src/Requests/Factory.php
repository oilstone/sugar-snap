<?php

namespace Api\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    /**
     * @return Request
     */
    public static function make()
    {
        return new Request(static::psr7ServerRequest(), static::parser());
    }

    /**
     * @return Parser
     */
    public static function parser()
    {
        return new Parser();
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function psr7ServerRequest()
    {
        $psr17Factory = new Psr17Factory();
        return (new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->fromGlobals();
    }
}