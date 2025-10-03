<?php

namespace Modules\Catalog\Repositories;

use App\Contracts\Repository\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        protected Product $model
    ) {}

    public function getPaginated(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->with('category')
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function find(int $id): ?object
    {
        return $this->model->with('category')->find($id);
    }

    public function search(string $term, int $limit = 10): array
    {
        if (strlen($term) < 2) {
            return [];
        }

        $products = $this->model->with('category')
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            })
            ->limit($limit)
            ->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? '',
            ];
        })->toArray();
    }

    public function findMany(array $ids): Collection
    {
        return $this->model->with('category')->whereIn('id', $ids)->get()->keyBy('id');
    }

    public function getByCategoryId(int $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)->get();
    }
}
