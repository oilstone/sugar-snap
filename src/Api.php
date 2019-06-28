<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Closure;
use Stitch\Stitch;

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
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public static function connect(string $host, string $database, string $username, string $password)
    {
        Stitch::setDatabaseHost($host);
        Stitch::connect($database, $username, $password);
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
    public static function handle($request)
    {
        $pipeline = (new Pipeline())->resolve($request);

        return $pipeline->current()->get($request, $pipeline);
    }
}