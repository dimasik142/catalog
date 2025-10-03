<?php

namespace Modules\Order\Repositories;

use App\Contracts\Repository\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected Order $model
    ) {}

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function findWithItems(int $id): ?object
    {
        return $this->model->with('orderItems')->find($id);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByCustomerEmail(string $email): Collection
    {
        return $this->model->where('customer_email', $email)->get();
    }
}
