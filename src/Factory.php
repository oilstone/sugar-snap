<?php

namespace Api;

use Api\Repositories\Stitch\Repository as StitchRepository;
use Api\Resources\Collection;
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
     * @return Collection
     */
    public static function collection($value)
    {
        if ($value instanceof Closure) {
            return new Collection(
                new StitchRepository(Stitch::make($value))
            );
        }

        return new Collection($value);
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