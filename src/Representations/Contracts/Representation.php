<?php

namespace Api\Representations\Contracts;

use Api\Requests\Request;

/**
 * Interface Representation
 * @package Api\Representations\Contracts
 */
interface Representation
{
    /**
     * @param Request $request
     * @param array $collection
     * @return mixed
     */
    public function forCollection(Request $request, array $collection);

    /**
     * @param Request $request
     * @param array $item
     * @return mixed
     */
    public function forSingleton(Request $request, array $item);
}