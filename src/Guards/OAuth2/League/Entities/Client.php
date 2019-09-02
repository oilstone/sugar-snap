<?php

namespace Api\Guards\OAuth2\League\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use Stitch\Records\Record;

class Client implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    /**
     * Client constructor.
     * @param Record $record
     */
    public function __construct(Record $record)
    {
        $this->setIdentifier($record->id);

        $this->name = $record->name;
        $this->redirectUri = array_map(function ($item)
        {
            return $item->uri;
        }, $record->redirects->toArray());
    }
}
