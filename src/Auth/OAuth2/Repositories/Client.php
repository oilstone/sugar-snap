<?php

namespace Api\Auth\OAuth2\Repositories;

use Api\Auth\OAuth2\Entities\Client as Entity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Stitch\Stitch;


class Client implements ClientRepositoryInterface
{
    protected $model;

    protected $record;

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
        $query =  $this->getModel()->where('id', $clientIdentifier)->get();

        if ($clientSecret) {
            $query->where('secret', $clientSecret);
        }

        return new Entity($query->first()());
    }

    /**
     * @return Client
     */
    protected function getModel()
    {
        return $this->model ? $this->model : $this->makeModel();
    }

    /**
     * @return $this
     */
    protected function makeModel()
    {
        $this->model = Stitch::make(function ($table)
        {
            $table->name('oauth_clients');
            $table->integer('id')->primary();
            $table->string('name');
            $table->string('secret');
            $table->string('redirect_uri');
        });

        return $this;
    }
}