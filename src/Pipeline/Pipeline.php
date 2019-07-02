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
        $pipe = new Pipe($this->request);
        $this->pipes[] = $pipe;

        return $pipe;
    }

    /**
     * @return $this
     */
    protected function assemble()
    {
        $pipe = null;

        foreach ($this->request->segments() as $piece) {
            if ($pipe && !$pipe->hasKey()) {
                if ($pipe->isCollectable()) {
                    $pipe->setKey($piece);

                    continue;
                }
            }

            $pipe = $this->makePipe();

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
        $this->assemble()->call();

        return $this;
    }

    /**
     * @return $this
     */
    protected function call()
    {
        foreach ($this->pipes as $pipe) {
            $pipe->call($this);
        }

        return $this;
    }
}