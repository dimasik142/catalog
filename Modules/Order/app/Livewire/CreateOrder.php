<?php

namespace Modules\Order\Livewire;

use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

class CreateOrder extends Component
{
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

    public function mount()
    {
        $this->cart = session()->get('order_cart', []);
    }

    public function searchProducts()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];

            return;
        }

        // Search for products without importing the Catalog module
        $this->searchResults = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.name', 'like', '%'.$this->search.'%')
            ->orWhere('products.description', 'like', '%'.$this->search.'%')
            ->select('products.*', 'categories.name as category_name')
            ->limit(10)
            ->get();
    }

    public function addToCart($productId)
    {
        $product = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $productId)
            ->select('products.*', 'categories.name as category_name')
            ->first();

        if (! $product) {
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
            $this->cart[$existingIndex]['quantity']++;
            $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['quantity'] * $this->cart[$existingIndex]['product_price'];
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
            ];
        }

        session()->put('order_cart', $this->cart);
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
            $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['product_price'];
        }

        session()->put('order_cart', $this->cart);
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        session()->put('order_cart', $this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('order_cart');
    }

    public function getTotal()
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function submitOrder()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty. Please add products before submitting.');

            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'total' => $this->getTotal(),
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            $this->clearCart();
            session()->flash('success', 'Order placed successfully! Order #'.$order->id);

            return redirect()->route('order.view', ['id' => $order->id]);
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create order. Please try again.');
        }
    }

    public function render()
    {
        return view('order::livewire.create-order');
    }
}
