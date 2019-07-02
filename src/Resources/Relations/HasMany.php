<?php

namespace Api\Resources\Relations;

use Stitch\Model;
use Stitch\Relations\Has;

/**
 * Class HasMany
 * @package Api\Resources\Relations
 */
class HasMany extends Relation
{
    /**
     * @return Model
     */
    protected function getLocalModel()
    {
        return $this->getLocalResource()->getRepository()->getModel();
    }

    /**
     * @return Model
     */
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

    /**
     * @return $this
     */
    public function pullKeys()
    {
        $localTable = $this->getLocalModel()->getTable();

        $key = $this->getForeignModel()->getTable()->getForeignKeyFor(
            $localTable->getName(),
            $localTable->getPrimaryKey()->getName()
        );

        $this->foreignKey = $key->getLocalColumn()->getName();
        $this->localKey = $key->getReferenceColumnName();

        return $this;
    }
}