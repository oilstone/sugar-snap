<?php

namespace Api\Guards\OAuth2\League\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Api\Guards\OAuth2\League\Entities\User as Entity;

class User implements UserRepositoryInterface
{
    protected $baseRepository;

    /**
     * User constructor.
     * @param $baseRepository
     */
    public function __construct($baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $id = $this->baseRepository->getIdByCredentials($username, $password);

        if ($id === null) {
            return null;
        }

        return new Entity($id);
    }
}
