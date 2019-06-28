<?php

namespace Api\Pipeline;

use Api\Registry;
use Api\Requests\Request;
use Api\Resources\Relations\Relation;
use Api\Resources\Resource;

/**
 * Class Pipeline
 * @package Api\Pipeline
 */
class Pipeline
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param Request $request
     * @return $this
     */
    public function resolve(Request $request)
    {
        $pairs = array_chunk($request->segments(), 2);
        $last = array_pop($pairs);

        /** @var Resource|null $resource */
        $resource = null;

        foreach ($pairs as $pair) {
            $resource = $resource ? $resource->getRelation($pair[0]) : Registry::get($pair[0]);

            $this->items[] = (new Ancestor($resource))->setData(
                $resource->find($pair[1])
            );
        }

        $current = $resource ? $resource->getRelation($last[0]) : Registry::get($last[0]);

        $this->items[] = $current instanceof Relation ? $current->getForeignResource() : $current;

        return $this;
    }

    /**
     * @return array
     */
    public function ancestors()
    {
        $count = count($this->items);

        return $count > 1 ? array_slice($this->items, 0, -1) : [];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @return mixed|null
     */
    public function current()
    {
        $count = count($this->items);

        return $count ? $this->items[$count - 1] : null;
    }
}