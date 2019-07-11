<?php

namespace Api\Exceptions;

use JsonSerializable;

class Payload implements JsonSerializable
{
    protected $message;

    /**
     * @param string $message
     * @return $this
     */
    public function message(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'message' => $this->message
        ];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}