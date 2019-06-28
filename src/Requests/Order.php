<?php

namespace Api\Requests;

class Order
{
    protected $property;

    protected $direction;

    public static function parse(string $input)
    {
        $direction = 'ASC';
        $operator = substr($input, 0, 1);

        if ($operator == '-' || $operator == '+') {
            if ($operator == '-') {
                $direction = 'DESC';
            }

            $input = substr($input, 1);
        }

        return (new static())->setProperty($input)->setDirection($direction);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property)
    {
        $this->property = $property;

        return $this;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setDirection(string $direction)
    {
        $this->direction = $direction;

        return $this;
    }
}