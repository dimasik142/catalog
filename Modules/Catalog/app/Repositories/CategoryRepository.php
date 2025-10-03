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
}
