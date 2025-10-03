<?php

namespace Modules\Order\Livewire;

use App\Contracts\Manager\OrderManagerInterface;
use App\Contracts\Repository\ProductRepositoryInterface;
use Exception;
use Livewire\Component;

class CreateOrder extends Component
{
    protected ProductRepositoryInterface $productRepository;

    protected OrderManagerInterface $orderManager;

    public $customer_name = '';

    public $customer_email = '';

    public $customer_phone = '';

    public $search = '';

    public $cart = [];

    public $searchResults = [];

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => 'required|string|max:255',
    ];

    public function boot(ProductRepositoryInterface $productRepository, OrderManagerInterface $orderManager): void
    {
        $this->productRepository = $productRepository;
        $this->orderManager = $orderManager;
    }

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function searchProducts()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];

            return;
        }

        // Use ProductService interface to search products (decoupled from Catalog module)
        $this->searchResults = $this->productRepository->search($this->search, 10);
    }

    public function addToCart($productId)
    {
        // Use ProductService interface to get product data (decoupled from Catalog module)
        $product = $this->productRepository->find($productId);

        if (! $product || $product['stock'] <= 0) {
            session()->flash('error', 'Product not available');

            return;
        }

        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            if ($this->cart[$existingIndex]['quantity'] >= $product['stock']) {
                session()->flash('error', 'Cannot add more items than available in stock');

                return;
            }
            $this->cart[$existingIndex]['quantity']++;
        } else {
            $this->cart[] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'stock' => $product['stock'],
            ];
        }

        session()->put('cart', $this->cart);
        session()->flash('success', 'Product added to cart');
        $this->search = '';
        $this->searchResults = [];
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
        } else {
            $this->cart[$index]['quantity'] = $quantity;
        }

        session()->put('cart', $this->cart);
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        session()->put('cart', $this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('cart');
    }

    public function getTotal()
    {
        return array_sum(array_map(function ($item) {
            $price = $item['price'] ?? $item['product_price'] ?? 0;

            return $price * $item['quantity'];
        }, $this->cart));
    }

    public function submitOrder()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty. Please add products before submitting.');

            return;
        }

        try {
            $customerData = [
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
            ];

            $order = $this->orderManager->createOrder($customerData, $this->cart);

            $this->clearCart();
            session()->flash('success', 'Order placed successfully! Order #'.$order->id);

            return redirect()->route('order.view', ['id' => $order->id]);
        } catch (Exception $e) {
            session()->flash('error', 'Failed to create order. Please try again.');
        }
    }

    public function render()
    {
        $this->searchProducts();

        return view('order::livewire.create-order');
    }
}
