<?php

namespace Api\Auth\OAuth2;

use Api\Auth\OAuth2\League\Factory as LeagueFactory;

class Factory
{
    protected static $publicKeyPath;

    protected static $privateKeyPath;

    protected static $encryptionKey;

    protected static $grants = [];

    /**
     * @param string $path
     */
    public static function setPublicKeyPath(string $path)
    {
        static::$publicKeyPath = $path;
    }

    /**
     * @param string $path
     */
    public static function setPrivateKeyPath(string $path)
    {
        static::$privateKeyPath = $path;
    }

    /**
     * @param string $key
     */
    public static function setEncryptionKey(string $key)
    {
        static::$encryptionKey = $key;
    }

    /**
     * @param string $name
     */
    public static function addGrant(string $name)
    {
        if (!in_array($name, static::$grants)) {
            static::$grants[] = $name;
        }
    }

    /**
     * @return League\Servers\Resource
     */
    public static function resourceServer()
    {
        return LeagueFactory::resourceServer(static::$publicKeyPath);
    }

    /**
     * @return League\Servers\Authorisation
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function AuthorisationServer()
    {
        return LeagueFactory::authorisationServer(static::$privateKeyPath, static::$encryptionKey, static::$grants);
    }
}
