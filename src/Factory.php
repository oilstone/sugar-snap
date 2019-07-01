<?php

namespace Api;

use Api\Repositories\Stitch\Repository as StitchRepository;
use Api\Resources\Collectable;
use Closure;
use Stitch\Stitch;

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
        if ($value instanceof Closure) {
            return new Collectable(
                new StitchRepository(Stitch::make($value))
            );
        }

        return new Collectable($value);
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function singleton(string $name, Closure $callback)
    {
        echo 'make singleton';
    }
}