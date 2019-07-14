<?php

namespace Api;

use Api\Config\Config;
use Api\Pipeline\Pipeline;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Representations\Representation;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Guards\OAuth2\Factory as GuardFactory;
use Api\Exceptions\Handler as ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
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

    protected static $configs = [];

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
     * @param Config $config
     */
    public static function addConfig(Config $config)
    {
        static::$configs[$config->getName()] = $config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function getConfig(string $name)
    {
        return static::$configs[$name] ?? null;
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function configure(string $name, Closure $callback)
    {
        $callback(static::$configs[$name]);
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
     *
     */
    public static function authorise()
    {
        static::try(function ()
        {
            static::respond(
                GuardFactory::authoriser(static::$request)
                    ->formatResponse(ResponseFactory::response())
            );
        });
    }

    /**
     *
     */
    public static function run(): void
    {
        static::try(function ()
        {
            $pipeline = (new Pipeline(static::$request))->assemble();
            GuardFactory::sentinel(static::$request, $pipeline)->protect();

            static::respond(ResponseFactory::json(
                $pipeline->call()->last()->getData()
            ));
        });
    }

    /**
     * @param Closure $callback
     */
    protected static function try(Closure $callback)
    {
        try {
            $callback();
        } catch (Exception $e) {
            (new ExceptionHandler(
                ResponseFactory::json(),
                ResponseFactory::emitter()
            ))->handle($e);
        }
    }

    /**
     * @param ResponseInterface $response
     */
    public static function respond(ResponseInterface $response)
    {
        ResponseFactory::emitter()->emit($response);
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

    /**
     *
     */
    public static function boot()
    {
        static::$request = RequestFactory::request();
        static::addConfig(RequestFactory::config());
        static::addConfig(GuardFactory::config());
    }
}
