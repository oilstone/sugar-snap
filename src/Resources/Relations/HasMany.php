<?php

namespace Api\Resources\Relations;

use Stitch\Relations\Has;
use Stitch\Queries\Relations\Has as Query;

class HasMany extends Relation
{
    public function query()
    {
        return (new Has(
            $this->getLocalResource()->getRepository()->getModel()
        ))->foreignModel(
            $this->getForeignResource()->getRepository()->getmodel()
        )->boot()->query();
    }
}