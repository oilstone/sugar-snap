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

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
                $pipe = $this->makePipe();

                if ($pipe->isCollectable()) {
                    $pipe->setKey($piece);

                    continue;
                }
            }

            if ($previous = $this->previous()) {
                $pipe->setEntity($previous->getRelation($piece))->scope($previous);
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

    public function flow()
    {
        $this->assemble()->call();

        return $this;
    }

    protected function call()
    {
        foreach ($this->pipes as $pipe) {
            $pipe->call($this->request);
        }
    }
}