<?php

namespace Api;

use Stitch\Model;

class UserRepository
{
    protected $model;

    /**
     * UserRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function getByCredentials($username, $password)
    {
        $record = $this->model->dehydrated()
            ->where('username', $username)
            ->where('password', $password)
            ->first();

        return $record ? $record->id : null;
    }
}
