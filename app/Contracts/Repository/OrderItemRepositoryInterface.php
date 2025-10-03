<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Collection;

interface OrderItemRepositoryInterface
{
    /**
     * Find order item by ID
     */
    public function find(int $id): ?object;

    /**
     * Get order items by order ID
     */
    public function getByOrderId(int $orderId): Collection;
}
