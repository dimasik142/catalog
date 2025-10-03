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
}
