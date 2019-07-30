<?php

namespace Api\Pipeline;

use Api\Resources\Registry;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Pipeline
 * @package Api\Pipeline
 */
class Pipeline
{
    protected $request;

    protected $resources;

    /**
     * @var array
     */
    protected $pipes = [];

    /**
     * Pipeline constructor.
     * @param ServerRequestInterface $request
     * @param Registry $resources
     */
    public function __construct(ServerRequestInterface $request, Registry $resources)
    {
        $this->request = $request;
        $this->resources = $resources;
    }

    /**
     * @return Pipe
     */
    protected function makePipe()
    {
        return new Pipe($this, $this->request);
    }

    /**
     * @return Pipe
     */
    protected function newPipe()
    {
        $pipe = $this->makePipe();

        $this->pipes[] = $pipe;

        return $pipe;
    }

    /**
     * @return $this
     */
    public function assemble()
    {
        /** @var Pipe $pipe */
        $pipe = null;

        foreach ($this->request->getAttribute('segments') as $segment) {
            if ($pipe && !$pipe->hasKey()) {
                if ($pipe->isCollectable()) {
                    $pipe->setKey(urldecode($segment));

                    continue;
                }
            }

            $pipe = $this->newPipe();

            if ($penultimate = $this->penultimate()) {
                $pipe->setEntity($penultimate->getResource()->getRelation($segment))->scope($penultimate);
            } else {
                $pipe->setEntity($this->resources->get($segment));
            }
        }

        return $this->classify();
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->pipes;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->pipes[count($this->pipes) - 1] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function penultimate()
    {
        return $this->pipes[count($this->pipes) - 2] ?? null;
    }

    /**
     * @param Pipe $pipe
     * @return array
     */
    public function before(Pipe $pipe)
    {
        return array_slice($this->pipes, 0, array_search($pipe, $this->pipes));
    }

    /**
     * @param Pipe $pipe
     * @return array
     */
    public function after(Pipe $pipe)
    {
        return array_slice($this->pipes, array_search($pipe, $this->pipes) + 1);
    }

    /**
     * @return $this
     */
    protected function classify()
    {
        foreach ($this->pipes as $pipe) {
            $pipe->classify();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function call()
    {
        foreach ($this->pipes as $pipe) {
            $pipe->call();
        }

        return $this;
    }
}