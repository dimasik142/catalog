<?php

namespace App\Contracts\Manager;

interface CategoryManagerInterface
{
    /**
     * Create a new category
     */
    public function create(array $data): object;

    /**
     * Update category
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete category
     */
    public function delete(int $id): bool;
}
