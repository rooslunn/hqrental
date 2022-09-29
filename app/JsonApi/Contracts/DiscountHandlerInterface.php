<?php

namespace App\JsonApi\Contracts;


interface DiscountHandlerInterface
{
    public function applyTo(array &$product): void;
}