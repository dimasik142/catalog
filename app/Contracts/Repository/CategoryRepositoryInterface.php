<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories with product count
     */
    public function getAllWithProductCount(): Collection;

    /**
     * Find category by ID
     */
    public function find(int $id): ?object;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?object;
}
