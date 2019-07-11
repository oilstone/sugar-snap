<?php

namespace Api\Auth\OAuth2;

use Api\Auth\OAuth2\League\Factory as LeagueFactory;
use Api\Config\Config;

class Factory
{
    /**
     * @return Config
     */
    public static function config(): Config
    {
        return (new Config('oauth'))->accepts(
            'publicKeyPath',
            'privateKeyPath',
            'encryptionKey',
            'grants'
        );
    }

    /**
     * @param Config $config
     * @return League\Servers\Resource
     */
    public static function resourceServer(Config $config)
    {
        return LeagueFactory::resourceServer($config);
    }

    /**
     * @param Config $config
     * @return League\Servers\Authorisation
     */
    public static function AuthorisationServer(Config $config)
    {
        return LeagueFactory::authorisationServer($config);
    }
}
