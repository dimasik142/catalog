<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Find order by ID
     */
    public function find(int $id): ?object;
}
