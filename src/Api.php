<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Representations\Representation;
use Api\Responses\Json as Response;
use Closure;
use Exception;
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
    public static function handle($request)
    {
        try {
            return new Response(
                (new Pipeline($request))->flow()->last()->getData()
            );
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }
    }

    /**
     * @return RepresentationContract
     */
    public static function getRepresentation(): RepresentationContract
    {
        return self::$representation ?? new Representation();
    }

    /**
     * @param RepresentationContract|string $representation
     */
    public static function setRepresentation($representation): void
    {
        if (!$representation instanceof $representation) {
            $representation = new $representation;
        }

        self::$representation = $representation;
    }
}