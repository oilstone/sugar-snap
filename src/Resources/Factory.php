<?php

namespace Api\Resources;

use Api\Factory as MasterFactory;
use Api\Resources\Relations\Collection as Relations;
use Api\Repositories\Stitch\Repository as StitchRepository;
use Stitch\Model;

class Factory
{
    protected $masterFactory;

    protected $registry;

    public function __construct(MasterFactory $masterFactory)
    {
        $this->masterFactory = $masterFactory;
    }

    /**
     * @return Registry
     */
    public function registry()
    {
        if (!$this->registry) {
            $this->registry = new Registry($this->masterFactory);
        }

        return $this->registry;
    }

    /**
     * @param $value
     * @return Collectable
     */
    public function collectable($value)
    {
        if ($value instanceof Model) {
            return new Collectable($this->masterFactory, new StitchRepository($value));
        }

        return new Collectable($this->masterFactory, $value);
    }

    /**
     * @param $value
     */
    public function singleton($value)
    {
        echo 'make singleton';
    }

    /**
     * @return Relations
     */
    public function relations()
    {
        return new Relations();
    }
}
