<?php

namespace Api\Resources\Relations;

use Stitch\Relations\Has;

/**
 * Class HasMany
 * @package Api\Resources\Relations
 */
class HasMany extends Relation
{
    protected function getLocalModel()
    {
        return $this->getLocalResource()->getRepository()->getModel();
    }

    protected function getForeignModel()
    {
        return $this->getForeignResource()->getRepository()->getmodel();
    }

    /**
     * @return mixed
     */
    public function query()
    {
        $relation = (new Has($this->getLocalModel()))->foreignModel($this->getForeignmodel());

        return $relation->boot()->query();
    }

    public function applyScope($ancestor)
    {
        $data = $ancestor->getData();
        $localModel = $this->getLocalModel();
        $primaryKey = $localModel->getTable()->getPtmaryKey()->getName();

        return $this->getForeignModel()->query()->where($primaryKey, $data[$primaryKey]);
    }
}