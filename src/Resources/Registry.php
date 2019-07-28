<?php

namespace Api\Resources;

use Api\Registry as AbstractRegistry;
use Api\Factory;
use Closure;


class Registry extends AbstractRegistry
{
    /**
     * @param string $name
     * @return mixed
     */
    public function resolve(string $name)
    {
        $item = $this->items[$name];

        if ($item instanceof Closure) {
            $item = $item(Factory::class)->name($name);
            $this->items[$name] = $item;
        }

        return $item;
    }
}