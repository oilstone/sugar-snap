<?php

namespace Api\Resources\Relations;

use Api\Registry;
use Closure;

/**
 * Class Collection
 * @package Api\Resources\Relations
 */
class Collection extends Registry
{
    /**
     * @param string $name
     * @return mixed
     */
    public function resolve(string $name)
    {
        if ($this->items[$name] instanceof Closure) {
            $this->items[$name] = $this->items[$name]();
        }

        return $this->items[$name];
    }
}