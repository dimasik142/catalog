<?php

namespace Modules\Order\Managers;

use App\Contracts\Manager\OrderItemManagerInterface;
use Modules\Order\Models\OrderItem;

class OrderItemManager implements OrderItemManagerInterface
{
    public function __construct(
        protected OrderItem $model,
    ) {}

    public function create(array $data): object
    {
        return $this->model->create($data);
    }
}
