<?php

namespace Api\Responses;

use Nyholm\Psr7\Factory\Psr17Factory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class Factory
{
    protected static $psr17Factory;

    /**
     * @param string $content
     * @return mixed
     */
    public static function response(string $content = '')
    {
        $response = static::psr7Response()->withBody(static::psr7Stream());

        if ($content) {
            $response->getBody()->write($content);
        }

        return $response;
    }

    /**
     * @param string $content
     * @return mixed
     */
    public static function json(string $content = '')
    {
        return static::response($content)->withHeader('Content-Type', 'application/json');
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