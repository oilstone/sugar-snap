<?php

namespace Api;

use Stitch\Stitch;
use Api\Repositories\Stitch\Repository as StitchRepository;
use Api\Resources\Collection;
use Closure;

class Factory
{
    public static function collection($value)
    {
        if ($value instanceof Closure) {
            return new Collection(
                new StitchRepository(Stitch::make($value))
            );
        }

        return new Collection($value);
    }

    public static function singleton(string $name, Closure $callback)
    {
        echo 'make singleton';
    }
}