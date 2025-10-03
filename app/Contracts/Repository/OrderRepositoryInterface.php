<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Find order by ID
     */
    public function find(int $id): ?object;

    /**
     * Find order with its items
     */
    public function findWithItems(int $id): ?object;

    /**
     * Get orders by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get orders by customer email
     */
    public function getByCustomerEmail(string $email): Collection;
}
