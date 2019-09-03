<?php

namespace Api\Repositories\Contracts;

use Api\Guards\OAuth2\Sentinel;
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
     * @return array
     */
    public function getByKey(Pipe $pipe): array;

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function getCollection(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array;

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function getRecord(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array;

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function create(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array;

    /**
     * @param Pipe $pipe
     * @param ServerRequestInterface $request
     * @param Sentinel $sentinel
     * @return array
     */
    public function update(Pipe $pipe, ServerRequestInterface $request, Sentinel $sentinel): array;
}
