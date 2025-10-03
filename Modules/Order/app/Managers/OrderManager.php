<?php

namespace Modules\Order\Managers;

use App\Contracts\Manager\OrderItemManagerInterface;
use App\Contracts\Manager\OrderManagerInterface;
use App\Contracts\Repository\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;

class OrderManager implements OrderManagerInterface
{
    public function __construct(
        protected Order $model,
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemManagerInterface $orderItemManager
    ) {}

    /**
     * Create an order with items in a transaction
     *
     * @param  array{customer_name: string, customer_email: string, customer_phone: string}  $customerData
     * @param  array<array{product_id: int, name: string, price: float, quantity: int}>  $cartItems
     *
     * @throws Exception
     */
    public function createOrder(array $customerData, array $cartItems): object
    {
        if (empty($cartItems)) {
            throw new Exception('Cart cannot be empty');
        }

        // Calculate total
        $total = array_sum(array_map(function ($item) {
            $price = $item['price'] ?? $item['product_price'] ?? 0;

            return $price * $item['quantity'];
        }, $cartItems));

        DB::beginTransaction();

        try {
            // Create order
            $order = $this->model->create([
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'],
                'total' => $total,
                'status' => Order::STATUS_PENDING,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                $price = $item['price'] ?? $item['product_price'];
                $name = $item['name'] ?? $item['product_name'];

                $this->orderItemManager->create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $name,
                    'product_price' => $price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $price * $item['quantity'],
                ]);
            }

            DB::commit();

            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $order = $this->orderRepository->find($id);

        if (! $order) {
            return false;
        }

        return $order->update(['status' => $status]);
    }
}

