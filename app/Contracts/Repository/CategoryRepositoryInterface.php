<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories with product count
     */
    public function getAllWithProductCount(): Collection;
}
