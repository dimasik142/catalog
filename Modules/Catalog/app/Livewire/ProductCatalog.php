<?php

namespace Modules\Catalog\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

class ProductCatalog extends Component
{
    use WithPagination;

    public $selectedCategoryId = null;

    public $search = '';

    protected $queryString = [
        'selectedCategoryId' => ['except' => null, 'as' => 'category'],
        'search' => ['except' => '', 'as' => 'q'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategoryId()
    {
        $this->resetPage();
    }

    public function filterByCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
    }

    public function clearFilters()
    {
        $this->selectedCategoryId = null;
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::withCount('products')->get();

        $productsQuery = Product::with('category')
            ->when($this->selectedCategoryId, function ($query) {
                $query->where('category_id', $this->selectedCategoryId);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc');

        $products = $productsQuery->paginate(12);

        return view('catalog::livewire.product-catalog', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
