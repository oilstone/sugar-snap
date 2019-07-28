<?php

namespace Api;

use Api\Config\Config;
use Api\Requests\Factory as RequestFactory;
use Api\Guards\OAuth2\Factory as GuardFactory;
use Psr\Http\Message\ServerRequestInterface;
use Stitch\Stitch;
use Closure;

/**
 * Class Api
 * @package Api
 */
class Package
{
    protected static $configs;

    /**
     * @param Closure $callback
     */
    public static function addConnection(Closure $callback)
    {
        Stitch::addConnection($callback);
    }

    /**
     * @return mixed
     */
    protected static function configs()
    {
        if (!static::$configs) {
            static::$configs = (new Configs())->put('request', RequestFactory::config())
                ->put('guard', GuardFactory::config());
        }

        return static::$configs;
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function configure(string $name, Closure $callback)
    {
        static::configs()->configure($name, $callback);
    }

    /**
     * @param null|ServerRequestInterface $request
     * @return Api
     */
    public static function make(?ServerRequestInterface $request = null)
    {
        return new Api(
            (new Configs())->inherit(static::configs()),
            $request
        );
    }
}
