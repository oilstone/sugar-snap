<?php

namespace Api\Auth\OAuth2\League;

use Api\Auth\OAuth2\League\Repositories\Client as ClientRepository;
use Api\Auth\OAuth2\League\Repositories\AccessToken as AccessTokenRepository;
use Api\Auth\OAuth2\League\Repositories\Scope as ScopeRepository;
use Api\Auth\OAuth2\League\Servers\Resource as ResourceServer;
use Api\Auth\OAuth2\League\Servers\Authorisation as AuthorisationServer;
use Api\Config\Config;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\ResourceServer as BaseResourceServer;
use League\OAuth2\Server\AuthorizationServer as BaseAuthorisationServer;
use Defuse\Crypto\Key;
use Stitch\Stitch;
use DateInterval;
use Exception;

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
            $table->integer('id')->primary();
            $table->string('client_id');
            $table->boolean('revoked');
            $table->datetime('expires_at');
        })->hasMany('scopes', Stitch::make(function ($table)
        {
            $table->name('oauth_access_token_scopes');
            $table->integer('id')->autoIncrement()->primary();
            $table->integer('oauth_access_token_id')->references('id')->on('oauth_access_tokens');
            $table->string('name');
        })));
    }

    /**
     * @return ScopeRepository
     */
    public static function scopeRepository()
    {
        return new ScopeRepository(Stitch::make(function ($table)
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
     * @param Config $config
     * @return ResourceServer
     */
    public static function resourceServer(Config $config)
    {
        return new ResourceServer(
            new BaseResourceServer(
                static::accessTokenRepository(),
                $config->get('publicKeyPath')
            )
        );
    }

    /**
     * @param Config $config
     * @return AuthorisationServer
     */
    public static function authorisationServer(Config $config)
    {
        $baseServer = new BaseAuthorisationServer(
            static::clientRepository(),
            static::accessTokenRepository(),
            static::scopeRepository(),
            $config->get('privateKeyPath'),
            Key::loadFromAsciiSafeString($config->get('encryptionKey'))
        );

        foreach ($config->get('grants') as $name) {
            $baseServer->enableGrantType(
                static::resolveGrant($name),
                new DateInterval('PT1H')
            );
        }

        return new AuthorisationServer($baseServer);
    }

    /**
     * @param string $name
     * @return ClientCredentialsGrant
     * @throws Exception
     */
    public static function resolveGrant(string $name)
    {
        switch ($name) {
            case 'client_credentials';
                return static::clientCredentialsGrant();
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
}
