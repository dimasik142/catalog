<?php

namespace Modules\Order\Livewire;

use Livewire\Component;
use Modules\Order\Models\Order;

class ViewOrder extends Component
{
    public Order $order;

    public function mount($id)
    {
        $this->order = Order::with('orderItems')->findOrFail($id);
    }

    public function render()
    {
        return view('order::livewire.view-order');
    }
}
