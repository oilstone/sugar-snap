<?php

namespace Api\Auth\OAuth2\League;

use Api\Auth\OAuth2\League\Repositories\Client as ClientRepository;
use Api\Auth\OAuth2\League\Repositories\AccessToken as AccessTokenRepository;
use Api\Auth\OAuth2\League\Repositories\Scope as ScopeRepository;
use Api\Auth\OAuth2\League\Servers\Resource as ResourceServer;
use League\OAuth2\Server\ResourceServer as BaseResourceServer;
use Stitch\Stitch;

class Factory
{
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
     * @return ResourceServer
     */
    public static function ResourceServer()
    {
        return new ResourceServer(
            new BaseResourceServer(
                static::accessTokenRepository(),
                __DIR__ . '/../public.key'
            )
        );
    }

    public static function AuthServer()
    {

    }
}