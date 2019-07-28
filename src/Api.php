<?php

namespace Api;

use Api\Config\Config;
use Api\Pipeline\Pipeline;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Guards\OAuth2\Factory as GuardFactory;
use Api\Exceptions\Handler as ExceptionHandler;
use Api\Resources\Registry as Registry;
use Psr\Http\Message\ResponseInterface;
use Stitch\Stitch;
use Closure;
use Exception;

/**
 * Class Api
 * @package Api
 */
class Api
{
    /**
     * @var
     */
    protected $registry;

    protected $configs;

    protected $requestFactory;

    protected $guardFactory;

    public function __construct($configs, $request = null)
    {
        $this->configs = $configs;
        $this->requestFactory = RequestFactory::instance($configs['request'], $request);

    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public function configure(string $name, Closure $callback)
    {
        $this->configs->configure($name, $callback);
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
                GuardFactory::authoriser(RequestFactory::request())
                    ->authoriseAndFormatResponse(ResponseFactory::response())
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
            $request = RequestFactory::resource();
            $pipeline = (new Pipeline($request))->assemble();
            GuardFactory::sentinel($request, $pipeline)->protect();

            static::respond(ResponseFactory::json(
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
                ResponseFactory::json(),
                ResponseFactory::emitter()
            ))->handle($e);
        }
    }

    /**
     * @param ResponseInterface $response
     */
    public function respond(ResponseInterface $response)
    {
        ResponseFactory::emitter()->emit($response);
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
