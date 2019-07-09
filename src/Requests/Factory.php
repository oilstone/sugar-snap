<?php

namespace Api\Requests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    public static function make()
    {
        $parser = new Parser();

        $psr17Factory = new Psr17Factory();
        $serverRequest = (new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->fromGlobals();

        return new Request($serverRequest, $parser);
    }
}