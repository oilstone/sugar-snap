<?php

namespace Api\Representations;

use Api\Representations\Contracts\Representation as RepresentationContract;
use Api\Requests\Request;

/**
 * Class Representation
 * @package Api\Representations
 */
class Representation implements RepresentationContract
{
    /**
     * @param Request $request
     * @param array $collection
     * @return mixed
     */
    public function forCollection(Request $request, array $collection)
    {
        return $collection;
    }

    /**
     * @param Request $request
     * @param array $item
     * @return mixed
     */
    public function forSingleton(Request $request, array $item)
    {
        return $item;
    }
}