<?php

namespace App\Contracts\Manager;

interface OrderManagerInterface
{
    /**
     * Create an order with items in a transaction
     *
     * @param  array{customer_name: string, customer_email: string, customer_phone: string}  $customerData
     * @param  array<array{product_id: int, name: string, price: float, quantity: int}>  $cartItems
     */
    public function createOrder(array $customerData, array $cartItems): object;

    /**
     * Update order
     */
    public function update(int $id, array $data): bool;

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Delete order
     */
    public function delete(int $id): bool;
}
