<?php

namespace Api\Pipeline;

use Api\Registry;
use Api\Requests\Request;

/**
 * Class Pipeline
 * @package Api\Pipeline
 */
class Pipeline
{
    /**
     * @var array
     */
    protected $pipes = [];

    protected $request;

    /**
     * Pipeline constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Pipe
     */
    protected function makePipe()
    {
        $pipe = new Pipe();
        $this->pipes[] = $pipe;

        return $pipe;
    }

    /**
     * @return $this
     */
    protected function assemble()
    {
        $pipe = $this->makePipe();

        foreach ($this->request->segments() as $piece) {
            if ($pipe->hasEntity()) {
                if ($pipe->isCollectable()) {
                    $pipe->setKey($piece);
                    $pipe = $this->makePipe();

                    continue;
                }

                $pipe = $this->makePipe();
            }

            if ($previous = $this->previous()) {
                $pipe->setEntity($previous->getResource()->getRelation($piece))->scope($previous);
            } else {
                $pipe->setEntity(Registry::get($piece));
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function ancestors()
    {
        $count = count($this->pipes);

        return $count > 1 ? array_slice($this->pipes, 0, -1) : [];
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
    public function current()
    {
        $count = count($this->pipes);

        return $count ? $this->pipes[$count - 1] : null;
    }

    /**
     * @return mixed|null
     */
    public function previous()
    {
        $count = count($this->pipes);

        return $count > 1 ? $this->pipes[$count - 2] : null;
    }

    /**
     * @return $this
     */
    public function flow()
    {
        $this->assemble()->resolve();

        return $this;
    }

    /**
     * @return $this
     */
    protected function resolve()
    {
        foreach ($this->ancestors() as $pipe) {
            $pipe->resolveResource();
        }

        $this->current()->call($this->request, );

        $pipe = $this->current();
        $pipe->getResource()->{$pipe->mehod()}($pipe->arguments());

        return $this;
    }
}