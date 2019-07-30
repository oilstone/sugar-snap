<?php

namespace Api;

use Api\Config\Store as Config;
use Api\Specs\Factory as SpecFactory;
use Api\Guards\Factory as GuardFactory;
use Psr\Http\Message\ServerRequestInterface;
use Stitch\Stitch;
use Closure;

/**
 * Class Api
 * @package Api
 */
class Package
{
    protected static $config;

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
    protected static function config()
    {
        if (!static::$config) {
            static::$config = (new Config())->put('specification', SpecFactory::config())
                ->put('guard', GuardFactory::config());
        }

        return static::$config;
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function configure(string $name, Closure $callback)
    {
        static::config()->configure($name, $callback);
    }

    /**
     * @param null|ServerRequestInterface $request
     * @return Api
     * @throws \Exception
     */
    public static function make(?ServerRequestInterface $request = null)
    {
        $factory = new Factory(static::config()->child());

        if ($request) {
            $factory->request()->setBaseRequest($request);
        }

        return new Api($factory);
    }
}
