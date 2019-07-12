<?php

namespace Api;

use Api\Config\Config;
use Api\Pipeline\Pipeline;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Representations\Representation;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Auth\OAuth2\Factory as AuthFactory;
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

    protected static $configs;

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
            static::$request = RequestFactory::request(static::getConfig('request'));
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

    /**
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function validateToken()
    {
        static::$request = AuthFactory::resourceServer(static::getConfig('oauth'))
            ->validate(static::request());
    }

    /**
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function guard()
    {
        static::validateToken();
    }

    /**
     *
     */
    public static function authorise()
    {
        static::try(function ()
        {
            static::respond(
                AuthFactory::AuthorisationServer(static::getConfig('oauth'))
                    ->issueToken(static::request(), ResponseFactory::response())
            );
        });
    }

    /**
     * @param ResponseInterface $response
     */
    public static function respond(ResponseInterface $response)
    {
        ResponseFactory::emitter()->emit($response);
    }

    /**
     *
     */
    public static function run(): void
    {
        static::try(function ()
        {
            static::guard();
            static::respond(ResponseFactory::json(static::evaluate()));
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
        static::addConfig(RequestFactory::config());
        static::addConfig(AuthFactory::config());
    }
}
