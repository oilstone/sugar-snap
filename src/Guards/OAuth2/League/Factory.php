<?php

namespace Api\Guards\OAuth2\League;

use Api\Guards\OAuth2\League\Repositories\Client as ClientRepository;
use Api\Guards\OAuth2\League\Repositories\AccessToken as AccessTokenRepository;
use Api\Guards\OAuth2\League\Repositories\RefreshToken as RefreshTokenRepository;
use Api\Guards\OAuth2\League\Repositories\Scope as ScopeRepository;
use Api\Guards\OAuth2\League\Repositories\User as UserRepository;
use Api\Config\Config;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\AuthorizationServer;
use Defuse\Crypto\Key;
use Stitch\Stitch;
use DateInterval;
use Exception;

class Factory
{
    protected static $config;

    /**
     * @return Config
     */
    public static function config(): Config
    {
        if (!static::$config) {
            static::$config = (new Config('oauth'))->accepts(
                'publicKeyPath',
                'privateKeyPath',
                'encryptionKey',
                'grants',
                'userRepository'
            );
        }

        return static::$config;
    }

    /**
     * @return ClientRepository
     */
    public static function clientRepository()
    {
        return new ClientRepository(Stitch::make(function ($table)
        {
            $table->name('oauth_clients');
            $table->string('id')->primary();
            $table->string('name');
            $table->string('secret');
        })->hasMany('redirects', Stitch::make(function ($table)
        {
            $table->name('oauth_client_redirects');
            $table->integer('id')->autoIncrement()->primary();
            $table->string('client_id')->references('id')->on('oauth_clients');
            $table->string('uri');
        })));
    }

    /**
     * @return AccessTokenRepository
     */
    public static function accessTokenRepository()
    {
        return new AccessTokenRepository(Stitch::make(function ($table)
        {
            $table->name('oauth_access_tokens');
            $table->string('id')->primary();
            $table->string('client_id');
            $table->integer('user_id');
            $table->boolean('revoked');
            $table->datetime('expires_at');
        }));
    }

    /**
     * @return RefreshTokenRepository
     */
    public static function refreshTokenRepository()
    {
        return new RefreshTokenRepository(Stitch::make(function ($table)
        {
            $table->name('oauth_refresh_tokens');
            $table->string('id')->primary();
            $table->string('oauth_access_token_id');
            $table->boolean('revoked');
            $table->datetime('expires_at');
        }));
    }

    /**
     * @return ScopeRepository
     */
    public static function scopeRepository()
    {
        return new ScopeRepository();
    }

    public static function userRepository($baseRepository)
    {
        return new UserRepository($baseRepository);
    }

    /**
     * @return ResourceServer
     */
    public static function resourceServer()
    {
        return new ResourceServer(
            static::accessTokenRepository(),
            static::$config->get('publicKeyPath')
        );
    }

    /**
     * @return AuthorizationServer
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function authorisationServer()
    {
        $server = new AuthorizationServer(
            static::clientRepository(),
            static::accessTokenRepository(),
            static::scopeRepository(),
            static::$config->get('privateKeyPath'),
            Key::loadFromAsciiSafeString(static::$config->get('encryptionKey'))
        );

        foreach (static::$config->get('grants') as $name) {
            $server->enableGrantType(static::grant($name), new DateInterval('PT1H'));
        }

        return $server;
    }

    /**
     * @param string $name
     * @param Config $config
     * @return ClientCredentialsGrant|PasswordGrant
     * @throws Exception
     */
    public static function grant(string $name)
    {
        switch ($name) {
            case 'client_credentials';
                return static::clientCredentialsGrant();

            case 'password';
                return static::passwordGrant(static::$config);
        }

        throw new Exception('Unsupported grant type');
    }

    /**
     * @return ClientCredentialsGrant
     */
    public static function clientCredentialsGrant()
    {
        return new ClientCredentialsGrant();
    }

    /**
     * @param Config $config
     * @return PasswordGrant
     * @throws Exception
     */
    public static function passwordGrant()
    {
        $grant = new PasswordGrant(
            static::userRepository(static::$config->get('userRepository')),
            static::refreshTokenRepository()
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1M'));

        return $grant;
    }
}
