<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Representations\Representation;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Auth\OAuth2\Factory as AuthFactory;
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
     * @param string $path
     */
    public static function publicKeyPath(string $path)
    {
        AuthFactory::setPublicKeyPath($path);
    }

    /**
     * @param string $path
     */
    public static function privateKeyPath(string $path)
    {
        AuthFactory::setPrivateKeyPath($path);
    }

    /**
     * @param string $key
     */
    public static function encryptionKey(string $key)
    {
        AuthFactory::setEncryptionKey($key);
    }

    /**
     * @param string $name
     */
    public static function enableGrant(string $name)
    {
        AuthFactory::addGrant($name);
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

//    public static function authorise()
//    {
//        static::$request = AuthFactory::AuthorisationServer()->validate(static::request());
//    }

    /**
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public static function authorise()
    {
        ResponseFactory::emitter()->emit(
            AuthFactory::AuthorisationServer()->issueToken(static::request(), ResponseFactory::make())
        );
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
            static::authorise();

//            static::authorise();
//            static::respond(static::evaluate());
        } catch (Exception $e) {
            (new ExceptionHandler(ResponseFactory::make(), ResponseFactory::emitter()))->handle($e);
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
