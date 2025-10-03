<?php

namespace Modules\Catalog\Livewire;

use App\Contracts\Repository\CategoryRepositoryInterface;
use App\Contracts\Repository\ProductRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property array $cart
 */
class ProductCatalog extends Component
{
    use WithPagination;

    public ?int $selectedCategoryId = null;

    public string $search = '';

    protected CategoryRepositoryInterface $categoryRepository;

    protected ProductRepositoryInterface $productRepository;

    protected $queryString = [
        'selectedCategoryId' => ['except' => null, 'as' => 'category'],
        'search' => ['except' => '', 'as' => 'q'],
    ];

    public function boot(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategoryId(): void
    {
        $this->resetPage();
    }

    public function filterByCategory($categoryId): void
    {
        $this->selectedCategoryId = $categoryId;
    }

    public function clearFilters(): void
    {
        $this->selectedCategoryId = null;
        $this->search = '';
        $this->resetPage();
    }

    public function addToCart($productId): void
    {
        $product = $this->productRepository->find($productId);

        if (! $product || $product->stock <= 0) {
            session()->flash('error', 'Product not available');

            return;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] >= $product->stock) {
                session()->flash('error', 'Cannot add more items than available in stock');

                return;
            }
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'stock' => $product->stock,
            ];
        }

        session()->put('cart', $cart);
        session()->flash('success', 'Product added to cart');
    }

    public function getCartProperty(): array
    {
        return session()->get('cart', []);
    }

    public function getCartCountProperty(): float|int
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function getCartTotalProperty(): float|int
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    public function render(): View
    {
        $categories = $this->categoryRepository->getAllWithProductCount();
        $products = $this->productRepository->getPaginated($this->selectedCategoryId, $this->search, 12);

        return view('catalog::livewire.product-catalog', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
