<?php

namespace App\Contracts\Manager;

interface ProductManagerInterface
{
    /**
     * Create a new product
     */
    public function create(array $data): object;

    /**
     * Update product
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete product
     */
    public function delete(int $id): bool;

    /**
     * Update product stock
     */
    public function updateStock(int $id, int $stock): bool;
}
