<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Representations\Representation;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Exceptions\Handler as ExceptionHandler;
use Stitch\Stitch;
use Closure;
use Exception;

/**
 * Class Api
 * @package Api
 */
class Api
{
    /**
     * @var
     */
    protected static $registry;

    /**
     * @var string
     */
    protected static $prefix = '';

    protected static $request;

    /**
     * @var RepresentationContract
     */
    protected static $representation;

    /**
     * @param Closure $callback
     */
    public static function addConnection(Closure $callback)
    {
        Stitch::addConnection($callback);
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function register(string $name, Closure $callback)
    {
        Registry::add($name, $callback);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function resolve(string $name)
    {
        return Registry::get($name);
    }

    /**
     * @return mixed
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function request()
    {
        if (static::$request === null) {
            static::$request = RequestFactory::make();
        }

        return static::$request;
    }

    /**
     * @return mixed
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function evaluate()
    {
        return (new Pipeline(static::request()))->flow()->last()->getData();
    }

    public static function authorise()
    {

    }

    public static function authenticate()
    {
        $request = static::request();
    }

    /**
     * @param string $data
     */
    public static function respond(string $data)
    {
        $response = ResponseFactory::make();
        $response->getBody()->write($data);

        ResponseFactory::emitter()->emit($response->withHeader('Content-Type', 'application/json'));
    }

    public static function run(): void
    {
        try {
            static::authenticate();
            static::respond(static::evaluate());
        } catch (Exception $e) {
            (new ExceptionHandler($e))->respond(ResponseFactory::make(), ResponseFactory::emitter());
        }
    }

    /**
     * @return RepresentationContract
     */
    public static function getRepresentation(): RepresentationContract
    {
        return static::$representation ?: new Representation();
    }

    /**
     * @param RepresentationContract|string $representation
     */
    public static function setRepresentation($representation): void
    {
        if (is_string($representation)) {
            $representation = new $representation;
        }

        static::$representation = $representation;
    }
}