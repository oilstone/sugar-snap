<?php

namespace Api\Representations\Contracts;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Representation
 * @package Api\Representations\Contracts
 */
interface Representation
{
    /**
     * @param ServerRequestInterface $request
     * @param array $collection
     * @return mixed
     */
    public function forCollection(ServerRequestInterface $request, array $collection);

    /**
     * @param ServerRequestInterface $request
     * @param array $item
     * @return mixed
     */
    public function forSingleton(ServerRequestInterface $request, array $item);
}