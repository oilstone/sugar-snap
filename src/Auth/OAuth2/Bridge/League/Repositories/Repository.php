<?php

namespace Api\Auth\OAuth2\Bridge\League\Repositories;

use Stitch\Model;

abstract class Repository
{
    protected $model;

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        if (!$this->model) {
            $this->model = $this->makeModel();
        }

        return $this->model;
    }

    /**
     * @return Model
     */
    abstract protected function makeModel(): Model;
}