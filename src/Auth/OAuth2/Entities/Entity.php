<?php

namespace Api\Auth\OAuth2\Entities;

use Stitch\Record;

class Entity
{
    protected $record;

    /**
     * Client constructor.
     * @param null|Record $record
     */
    public function __construct(?Record $record)
    {
        $this->record = $record;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    protected function getAttribute(string $name)
    {
        return $this->record ? $this->record->{$name} : null;
    }
}