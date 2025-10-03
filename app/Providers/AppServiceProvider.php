<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository interfaces (read-only operations)
        $this->app->singleton(
            \App\Contracts\Repository\CategoryRepositoryInterface::class,
            \Modules\Catalog\Repositories\CategoryRepository::class
        );

        $this->app->singleton(
            \App\Contracts\Repository\ProductRepositoryInterface::class,
            \Modules\Catalog\Repositories\ProductRepository::class
        );

        $this->app->singleton(
            \App\Contracts\Repository\OrderRepositoryInterface::class,
            \Modules\Order\Repositories\OrderRepository::class
        );

        // Bind Manager interfaces (write operations: create, update, delete)
        $this->app->singleton(
            \App\Contracts\Manager\OrderManagerInterface::class,
            \Modules\Order\Managers\OrderManager::class
        );

        $this->app->singleton(
            \App\Contracts\Manager\OrderItemManagerInterface::class,
            \Modules\Order\Managers\OrderItemManager::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
