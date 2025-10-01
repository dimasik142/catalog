<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('order.create') }}" class="text-blue-600 hover:underline">← Back to Create Order</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
            <span class="px-4 py-2 rounded-lg font-semibold
                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
            ">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <!-- Customer Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $order->customer_email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phone</p>
                    <p class="font-medium text-gray-900">{{ $order->customer_phone }}</p>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order Date</p>
                    <p class="font-medium text-gray-900">{{ $order->created_at->format('F j, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="font-medium text-gray-900">{{ $order->updated_at->format('F j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($item->product_price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Total -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center">
                <span class="text-2xl font-bold text-gray-900">Total:</span>
                <span class="text-3xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
