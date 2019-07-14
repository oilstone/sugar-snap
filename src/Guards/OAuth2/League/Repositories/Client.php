<?php

namespace Api\Guards\OAuth2\League\Repositories;

use Api\Guards\OAuth2\League\Entities\Client as Entity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Stitch\Model;


class Client implements ClientRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

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
        $query =  $this->model->with('redirects')->where('id', $clientIdentifier);

        if ($clientSecret || $mustValidateSecret) {
            $query->where('secret', $clientSecret);
        }

        return new Entity($query->first());
    }
}