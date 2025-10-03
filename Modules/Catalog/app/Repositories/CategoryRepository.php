<?php

namespace Modules\Catalog\Repositories;

use App\Contracts\Repository\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        protected Category $model
    ) {}

    public function getAllWithProductCount(): Collection
    {
        return $this->model->withCount('products')->get();
    }

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?object
    {
        return $this->model->where('slug', $slug)->first();
    }
}
