<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Exceptions\Handler as ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Closure;
use Exception;

/**
 * Class Api
 * @package Api
 */
class Api
{
    protected $factory;

    /**
     * @var
     */
    protected $resources;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->resources = $factory->resource()->registry();
    }

    /**
     * @param string $name
     * @param Closure $callback
     * @return $this
     */
    public function configure(string $name, Closure $callback)
    {
        $this->factory->configure($name, $callback);

        return $this;
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public function register(string $name, Closure $callback)
    {
        $this->resources->bind($name, $callback);
    }

    /**
     *
     */
    public function authorise()
    {
        $this->emitResponse($this->generateAuthorisationResponse());
    }

    /**
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    public function generateAuthorisationResponse()
    {
        return $this->try(function ()
        {
            return $this->factory->guard()->authoriser($this->factory->request()->base())
                ->authoriseAndFormatResponse($this->factory->response()->base());
        });
    }

    /**
     * @param Closure $callback
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    protected function try(Closure $callback)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            return (new ExceptionHandler(
                $this->factory->response()->json()
            ))->handle($e);
        }
    }

    /**
     * @param ResponseInterface $response
     */
    protected function emitResponse(ResponseInterface $response)
    {
        $this->factory->response()->emitter()->emit($response);
    }

    /**
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    public function generateResponse()
    {
        return $this->try(function ()
        {
            $request = $this->factory->request()->query();
            $pipeline = (new Pipeline($request, $this->resources))->assemble();
            $this->factory->guard()->sentinel($request, $pipeline)->protect();

            return $this->factory->response()->json(
                $pipeline->call()->last()->getData()
            );
        });
    }

    /**
     * @throws Exception
     */
    public function respond()
    {
        $this->emitResponse($this->generateResponse());
    }
}
