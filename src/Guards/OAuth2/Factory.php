<?php

namespace Api\Guards\OAuth2;

use Api\Guards\OAuth2\League\Factory as LeagueFactory;
use Api\Config\Config;

class Factory
{
    /**
     * @return Config
     */
    public static function config(): Config
    {
        return LeagueFactory::config();
    }

    /**
     * @param Config $config
     * @return LeagueFactory
     */
    public static function instance(Config $config)
    {
        return new LeagueFactory($config);
    }

    /**
     * @param $request
     * @param $pipeline
     * @return Sentinel
     */
    public static function sentinel($request, $pipeline)
    {
        return new Sentinel(
            LeagueFactory::resourceServer(),
            $request,
            $pipeline
        );
    }

    /**
     * @param $request
     * @return Authoriser
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function authoriser($request)
    {
        return new Authoriser(
            LeagueFactory::authorisationServer(),
            $request
        );
    }
}
