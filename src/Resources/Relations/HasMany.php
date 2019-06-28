<?php

namespace Api\Resources\Relations;

use Stitch\Relations\Has;

/**
 * Class HasMany
 * @package Api\Resources\Relations
 */
class HasMany extends Relation
{
    /**
     * @return mixed
     */
    public function query()
    {
        return (new Has(
            $this->getLocalResource()->getRepository()->getModel()
        ))->foreignModel(
            $this->getForeignResource()->getRepository()->getmodel()
        )->boot()->query();
    }
}