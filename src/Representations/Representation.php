<?php

namespace Api\Representations;

use Api\Representations\Contracts\Representation as RepresentationContract;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Representation
 * @package Api\Representations
 */
class Representation implements RepresentationContract
{
    /**
     * @param ServerRequestInterface $request
     * @param array $collection
     * @return array|mixed
     */
    public function forCollection(ServerRequestInterface $request, array $collection)
    {
        return $collection;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $item
     * @return array|mixed
     */
    public function forSingleton(ServerRequestInterface $request, array $item)
    {
        return $item;
    }
}