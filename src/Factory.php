<?php

namespace Api;

use Api\Config\Store as Configs;
use Api\Guards\OAuth2\Factory as GuardFactory;
use Api\Requests\Factory as RequestFactory;
use Api\Responses\Factory as ResponseFactory;
use Api\Repositories\Stitch\Repository as StitchRepository;
use Api\Resources\Collectable;
use Exception;
use Stitch\Stitch;
use Stitch\Model;
use Closure;

/**
 * Class Factory
 * @package Api
 */
class Factory
{
    protected $configs;

    protected $request;

    protected $response;

    protected $guard;

    /**
     * Factory constructor.
     * @param Configs|null $configs
     */
    public function __construct(?Configs $configs = null)
    {
        $this->configs = $configs;
    }

    /**
     * @param string $name
     * @param Closure $callback
     * @return $this
     */
    public function configure(string $name, Closure $callback)
    {
        $this->configs->configure($name, $callback);

        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     * @throws Exception
     */
    protected function getConfig(string $name)
    {
        if (!$this->configs->has($name)) {
            throw new Exception("No config found for '$name'");
        }

        return $this->configs->get($name);
    }

    /**
     * @return RequestFactory
     * @throws Exception
     */
    public function request()
    {
        if (!$this->request) {
            $config = $this->getConfig();

            $this->request = new RequestFactory($this->getConfig('request'));
        }

        return $this->request;
    }

    /**
     * @return ResponseFactory
     */
    public function response()
    {
        if (!$this->response) {
            $this->response = new ResponseFactory();
        }

        return $this->response;
    }

    /**
     * @return GuardFactory
     * @throws Exception
     */
    public function guard()
    {
        if (!$this->guard) {
            $this->guard = new GuardFactory($this->getConfig('guard'));
        }

        return $this->guard;
    }

    /**
     * @param $value
     * @return Collectable
     */
    public function collectable($value)
    {
        if ($value instanceof Model) {
            return new Collectable(
                new StitchRepository($value)
            );
        }

        return new Collectable($value);
    }

    /**
     * @param Closure $callback
     * @return Model
     */
    public function model(Closure $callback)
    {
        return Stitch::make($callback);
    }

    /**
     * @param $value
     */
    public function singleton($value)
    {
        echo 'make singleton';
    }
}
