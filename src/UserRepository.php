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
     * @param $id
     * @return mixed|null
     */
    public function getById($id)
    {
        $record = $this->model->find($id);

        return $record ? $record->toArray() : null;
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function getIdByCredentials($username, $password)
    {
        $record = $this->model->dehydrated()
            ->where('username', $username)
            ->where('password', $password)
            ->first();

        return $record ? $record->id : null;
    }
}
