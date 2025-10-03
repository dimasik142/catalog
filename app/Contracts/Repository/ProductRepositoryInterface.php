<?php

namespace App\Contracts\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get paginated products with optional filters
     */
    public function getPaginated(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator;

    /**
     * Find product by ID
     */
    public function find(int $id): ?object;

    /**
     * Search products by term
     */
    public function search(string $term, int $limit = 10): array;

    /**
     * Find many products by IDs
     */
    public function findMany(array $ids): Collection;

    /**
     * Get products by category ID
     */
    public function getByCategoryId(int $categoryId): Collection;
}
