<?php

namespace Api\Pipeline;

use Api\Registry;
use Api\Requests\Request;
use Api\Resources\Singleton;

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
     * @param Request $request
     * @return $this
     */
    protected function assemble()
    {
        $pipe = $this->makePipe();
        $pieces = $this->request->segments();

        while ($pieces) {
            $piece = array_shift($pieces);

            if ($pipe->hasEntity()) {
                $resource = $pipe->getEntity();
                $pipe = $this->makePipe();

                if (!$resource instanceof Singleton) {
                    $pipe->setKey($piece);

                    continue;
                }
            }

            $pipe->setEntity(
                count($this->pipes) > 1 ? $this->previous()->getRelation($piece) : Registry::get($piece)
            );
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

    public function flow()
    {
        return $this->assemble()->call();
    }

    protected function call()
    {
        foreach ($this->pipes as $pipe) {
            $pipe->call();
        }

        return $this->current();
    }
}