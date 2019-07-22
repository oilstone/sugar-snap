<?php

namespace Api\Repositories\Contracts;

use Api\Pipeline\Pipe;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Repository
 * @package Api\Repositories\Contracts
 */
interface Repository
{
    /**
     * @param Pipe $pipe
     * @return mixed
     */
    public function getByKey(Pipe $pipe);

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function getCollection(Pipe $pipe, ServerRequestInterface $request);

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function getRecord(Pipe $pipe, ServerRequestInterface $request);

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function create(Pipe $pipe, ServerRequestInterface $request);
}