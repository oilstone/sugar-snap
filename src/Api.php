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
     * @param $request
     * @return mixed
     */
    public static function evaluate($request)
    {
        return (new Pipeline($request))->flow()->last()->getData();
    }

    public static function authenticate($request)
    {

    }

    public static function respond($data)
    {
        return $data;
    }

    public static function run(): void
    {
        try {
            $request = RequestFactory::make();
            static::authenticate($request);
            static::respond(static::evaluate($request));
        } catch (Exception $e) {
            (new ExceptionHandler($e))->respond(ResponseFactory::make());
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