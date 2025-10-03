<?php

namespace Modules\Catalog\Managers;

use App\Contracts\Manager\CategoryManagerInterface;
use App\Contracts\Repository\CategoryRepositoryInterface;
use Modules\Catalog\Models\Category;

class CategoryManager implements CategoryManagerInterface
{
    public function __construct(
        protected Category $model,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->categoryRepository->find($id);

        if (! $category) {
            return false;
        }

        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        $category = $this->categoryRepository->find($id);

        if (! $category) {
            return false;
        }

        return $category->delete();
    }
}
