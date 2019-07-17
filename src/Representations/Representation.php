<?php

namespace Api\Representations;

use Api\Pipeline\Pipe;
use Api\Representations\Contracts\Representation as RepresentationContract;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Representation
 * @package Api\Representations
 */
class Representation implements RepresentationContract
{
    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param array $collection
     * @return array|mixed
     */
    public function forCollection(Pipe $pipe, ServerRequestInterface $request, array $collection)
    {
        return $collection;
    }

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param array $item
     * @return array|mixed
     */
    public function forSingleton(Pipe $pipe, ServerRequestInterface $request, array $item)
    {
        return $item;
    }
}