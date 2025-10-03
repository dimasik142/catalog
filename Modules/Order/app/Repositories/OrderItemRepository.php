<?php

namespace Modules\Order\Repositories;

use App\Contracts\Repository\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\OrderItem;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function __construct(
        protected OrderItem $model
    ) {}

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function getByOrderId(int $orderId): Collection
    {
        return $this->model->where('order_id', $orderId)->get();
    }
}
