<?php

namespace Api\Auth\OAuth2\Bridge\League\Repositories;

use Api\Auth\OAuth2\Bridge\League\Entities\Client as Entity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Stitch\Stitch;
use Stitch\Model;


class Client extends Repository implements ClientRepositoryInterface
{
    protected $model;

    /**
     * Get a client.
     *
     * @param string      $clientIdentifier   The client's identifier
     * @param null|string $grantType          The grant type used (if sent)
     * @param null|string $clientSecret       The client's secret (if sent)
     * @param bool        $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true): ClientEntityInterface
    {
        $query =  $this->getModel()->with('redirects')->where('id', $clientIdentifier)->get();

        if ($clientSecret) {
            $query->where('secret', $clientSecret);
        }

        return new Entity($query->first());
    }

    /**
     * @return Model
     */
    protected function makeModel(): Model
    {
        return Stitch::make(function ($table)
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
        }));
    }
}