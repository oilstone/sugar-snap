<?php

namespace Api;

use Api\Pipeline\Pipeline;
use Api\Exceptions\Handler as ExceptionHandler;
use Api\Resources\Registry as Registry;
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
    protected $registry;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->registry = new Registry();
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
        $this->registry->bind($name, $callback);
    }

    /**
     *
     */
    public function authorise()
    {
        $this->try(function ()
        {
            $this->respond(
                $this->factory->guard()->authoriser($this->factory->request()->base())
                    ->authoriseAndFormatResponse($this->factory->response()->base())
            );
        });
    }

    /**
     *
     */
    public function run(): void
    {
        $this->try(function ()
        {
            $request = $this->factory->request()->query();
            $pipeline = (new Pipeline($request))->assemble();
            $this->factory->guard()->sentinel($request, $pipeline)->protect();

            static::respond($this->factory->response()->json(
                $pipeline->call()->last()->getData()
            ));
        });
    }

    /**
     * @param Closure $callback
     */
    protected function try(Closure $callback)
    {
        try {
            $callback();
        } catch (Exception $e) {
            (new ExceptionHandler(
                $this->factory->response()->json(),
                $this->factory->response()->emitter()
            ))->handle($e);
        }
    }

    /**
     * @param ResponseInterface $response
     */
    public function respond(ResponseInterface $response)
    {
        $this->factory->response()->emitter()->emit($response);
    }

//    /**
//     * @return RepresentationContract
//     */
//    public static function getRepresentation(): RepresentationContract
//    {
//        return static::$representation ?: new Representation();
//    }
//
//    /**
//     * @param RepresentationContract|string $representation
//     */
//    public static function setRepresentation($representation): void
//    {
//        if (is_string($representation)) {
//            $representation = new $representation;
//        }
//
//        static::$representation = $representation;
//    }
}
