<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Product Catalog</h1>

        <!-- Search Bar -->
        <div class="mb-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search products..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        <!-- Categories Filter -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button
                wire:click="clearFilters"
                class="px-4 py-2 rounded-lg transition {{ $selectedCategoryId === null ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
            >
                All Products
            </button>
            @foreach ($categories as $category)
                <button
                    wire:click="filterByCategory({{ $category->id }})"
                    class="px-4 py-2 rounded-lg transition {{ $selectedCategoryId == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    {{ $category->name }} ({{ $category->products_count }})
                </button>
            @endforeach
        </div>

        @if ($search || $selectedCategoryId)
            <div class="text-sm text-gray-600">
                Showing {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                @if ($search)
                    matching "{{ $search }}"
                @endif
                @if ($selectedCategoryId)
                    in {{ $categories->firstWhere('id', $selectedCategoryId)->name }}
                @endif
                <button wire:click="clearFilters" class="text-blue-600 hover:underline ml-2">Clear filters</button>
            </div>
        @endif
    </div>

    <!-- Products Grid -->
    @if ($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach ($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 flex-1">{{ $product->name }}</h3>
                            @if ($product->stock === 0)
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Out of Stock</span>
                            @elseif ($product->stock < 10)
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Low Stock</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mb-3">{{ $product->category->name }}</p>

                        @if ($product->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $product->description }}</p>
                        @endif

                        <div class="flex items-center justify-between mt-4">
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if ($search || $selectedCategoryId)
                    Try adjusting your filters or search terms.
                @else
                    Get started by adding products in the admin panel.
                @endif
            </p>
        </div>
    @endif
</div>
