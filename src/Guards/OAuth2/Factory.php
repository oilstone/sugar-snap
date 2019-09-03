<?php

namespace Api\Guards\OAuth2;

use Api\Guards\OAuth2\League\Factory as LeagueFactory;
use Api\Config\Service;

class Factory
{
    protected $config;

    protected $leagueFactory;

    public function __construct(Service $config)
    {
        $this->config = $config;
        $this->leagueFactory = new LeagueFactory($config);
    }

    /**
     * @return Service
     */
    public static function config()
    {
        return LeagueFactory::config()->accepts('userRepository');
    }

    /**
     * @param $request
     * @param $pipeline
     * @return Sentinel
     */
    public function sentinel($request, $pipeline)
    {
        return new Sentinel(
            $request,
            $pipeline,
            $this->key($request)
        );
    }

    public function key($request)
    {
        return new Key(
            $this->leagueFactory->resourceServer(),
            $request,
            $this->config->get('userRepository')
        );
    }

    /**
     * @param $request
     * @return Authoriser
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function authoriser($request)
    {
        return new Authoriser(
            $this->leagueFactory->authorisationServer(),
            $request
        );
    }
}
