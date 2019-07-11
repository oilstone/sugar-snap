<?php

namespace Api\Responses;

use Nyholm\Psr7\Factory\Psr17Factory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class Factory
{
    protected static $psr17Factory;

    /**
     * @return mixed
     */
    public static function make()
    {
        return static::psr7Response()->withBody(static::psr7Stream());
    }

    /**
     * @return mixed
     */
    public static function psr17Factory()
    {
        if (static::$psr17Factory) {
            return static::$psr17Factory;
        }

        static::$psr17Factory = new Psr17Factory();

        return static::$psr17Factory;
    }

    /**
     * @return mixed
     */
    public static function psr7Response()
    {
        return static::psr17Factory()->createResponse();
    }

    /**
     * @return mixed
     */
    public static function psr7Stream()
    {
        return static::psr17Factory()->createStream();
    }

    /**
     * @return SapiEmitter
     */
    public static function emitter()
    {
        return new SapiEmitter();
    }
}