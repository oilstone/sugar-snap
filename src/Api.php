<?php

namespace Api;

use Stitch\Stitch;
use Api\Pipeline\Pipeline;
use Closure;

class Api
{
    protected static $registry;

    protected static $prefix = '';

    public static function connect(string $database, string $username, string $password)
    {
        Stitch::Connect($database, $username, $password);
    }

    public static function register(string $name, Closure $callback)
    {
        Registry::add($name, $callback);
    }

    public static function resolve(string $name)
    {
        return Registry::get($name);
    }

    public static function handle($request)
    {
        $pipeline = (new Pipeline())->resolve($request);

        return $pipeline->current()->get($request, $pipeline);
    }
}