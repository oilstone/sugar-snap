<?php

namespace Api;

use Api\Repositories\Stitch\Repository as StitchRepository;
use Api\Resources\Collectable;
use Closure;
use Stitch\Stitch;
use Stitch\Model;

/**
 * Class Factory
 * @package Api
 */
class Factory
{
    /**
     * @param $value
     * @return Collectable
     */
    public static function collectable($value)
    {
        if ($value instanceof Model) {
            return new Collectable(
                new StitchRepository($value)
            );
        }

        return new Collectable($value);
    }

    /**
     * @param Closure $callback
     * @return Model
     */
    public static function model(Closure $callback)
    {
        return Stitch::make($callback);
    }

    /**
     * @param $value
     */
    public static function singleton($value)
    {
        echo 'make singleton';
    }
}