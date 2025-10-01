<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Order</h1>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                    <input
                        type="text"
                        wire:model="customer_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('customer_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Email *</label>
                    <input
                        type="email"
                        wire:model="customer_email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('customer_email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone *</label>
                    <input
                        type="tel"
                        wire:model="customer_phone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('customer_phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

            @if (count($cart) > 0)
                <div class="space-y-3 mb-4">
                    @foreach ($cart as $index => $item)
                        <div class="flex items-center justify-between border-b pb-3">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $item['product_name'] }}</h3>
                                <p class="text-sm text-gray-600">${{ number_format($item['product_price'], 2) }} each</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input
                                    type="number"
                                    wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                    value="{{ $item['quantity'] }}"
                                    min="1"
                                    class="w-16 px-2 py-1 border border-gray-300 rounded"
                                >
                                <span class="text-gray-900 font-medium w-20 text-right">${{ number_format($item['subtotal'], 2) }}</span>
                                <button
                                    wire:click="removeFromCart({{ $index }})"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-gray-900">Total:</span>
                        <span class="text-2xl font-bold text-gray-900">${{ number_format($this->getTotal(), 2) }}</span>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button
                        wire:click="clearCart"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Clear Cart
                    </button>
                    <button
                        wire:click="submitOrder"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >
                        Place Order
                    </button>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p>Your cart is empty</p>
                    <p class="text-sm">Add products below to get started</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Search -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Add Products</h2>

        <div class="mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search products by name..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        @if (count($searchResults) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($searchResults as $product)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                        <h3 class="font-medium text-gray-900 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $product->category_name }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            <button
                                wire:click="addToCart({{ $product->id }})"
                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
                            >
                                Add to Cart
                            </button>
                        </div>
                        @if ($product->stock < 10)
                            <p class="text-xs text-yellow-600 mt-2">Only {{ $product->stock }} left in stock</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif ($search)
            <p class="text-center text-gray-500 py-4">No products found matching "{{ $search }}"</p>
        @endif
    </div>
</div>
