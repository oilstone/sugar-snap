<?php

namespace Api\Resources;


/**
 * Class Collection
 * @package Api\Resources
 */
class Collectable extends Resource
{
    protected const ENDPOINTS = [
        'index',
        'show',
        'create',
        'update',
        'destroy'
    ];
}