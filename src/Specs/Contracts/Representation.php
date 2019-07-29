<?php

namespace Api\Specs\Contracts;

use Api\Pipeline\Pipe;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Representation
 * @package Api\Representations\Contracts
 */
interface Representation
{
    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param array $collection
     * @return mixed
     */
    public function forCollection(Pipe $pipe, ServerRequestInterface $request, array $collection);

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param array $item
     * @return mixed
     */
    public function forSingleton(Pipe $pipe, ServerRequestInterface $request, array $item);
}