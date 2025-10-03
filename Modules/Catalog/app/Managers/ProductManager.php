<?php

namespace Modules\Catalog\Managers;

use App\Contracts\Manager\ProductManagerInterface;
use App\Contracts\Repository\ProductRepositoryInterface;
use Modules\Catalog\Models\Product;

class ProductManager implements ProductManagerInterface
{
    public function __construct(
        protected Product $model,
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->productRepository->find($id);

        if (! $product) {
            return false;
        }

        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->productRepository->find($id);

        if (! $product) {
            return false;
        }

        return $product->delete();
    }

    public function updateStock(int $id, int $stock): bool
    {
        $product = $this->productRepository->find($id);

        if (! $product) {
            return false;
        }

        return $product->update(['stock' => $stock]);
    }
}
