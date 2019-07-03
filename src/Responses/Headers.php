<?php

namespace Api\Responses;

class Headers
{
    protected $items = [];

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function set(string $name, string $value)
    {
        $this->items[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function contentTypeJson()
    {
        $this->set('content-type', 'application/json');

        return $this;
    }

    /**
     * @return $this
     */
    public function send()
    {
        foreach ($this->items as $key => $value) {
            header("$key: $value");
        }

        return $this;
    }
}
