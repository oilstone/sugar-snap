<?php

namespace Api\Config;

use Api\Collection as BaseCollection;
use Closure;

class Collection extends BaseCollection
{
    /**
     * @param Collection $collection
     * @return $this
     */
    public function inherit(Collection $collection)
    {
        foreach ($collection as $name => $config) {
            $this->put(
                $name,
                (new Config())->inherit($config)
            );
        }

        return $this;
    }

    public function configure(string $name, Closure $callback)
    {
        $callback($this->items[$name]);
    }
}
