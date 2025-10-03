<?php

namespace App\Contracts\Manager;

interface OrderItemManagerInterface
{
    /**
     * Create a new order item
     */
    public function create(array $data): object;
}
