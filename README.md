# Laravel Modular E-Commerce Platform

A modern Laravel 12 application featuring a modular architecture with Catalog and Order management modules, built with Filament 4, Livewire 3, and comprehensive code quality tools.

## Setup Instructions

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd catalog
   ```

2. **Start Docker Services**
   ```bash
   docker-compose up -d
   ```

3. **Install Dependencies**
   ```bash
   docker-compose exec app composer install
   ```

### Database Setup

1. **Generate Application Key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

2. **Run Migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

3. **Seed Database (Optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

### Environment Configuration

The `.env` file is already configured for Docker. Key settings:

- **Database**: PostgreSQL 16 (via Docker)
  - Host: `db`
  - Port: `5432`
  - Database: `laravel`
  - User: `laravel`
  - Password: `secret`

- **Application URL**: `http://localhost:8888`

## Running the Application

### Starting the Application

```bash
# Start all containers
docker-compose up -d

# Or use Makefile
make up
```

### Accessing Admin Interfaces

1. **Create Admin User**
   ```bash
   docker-compose exec app php artisan make:filament-user
   ```

2. **Access Points**
   - **Filament Admin Panel**: http://localhost:8888/admin
   - **Public Catalog**: http://localhost:8888/catalog
   - **Create Order**: http://localhost:8888/order/create

### Testing Main Functionality

1. **Catalog Module**
   - Browse products at `/catalog`
   - Filter by category
   - Search products
   - Add items to cart

2. **Order Module**
   - Create orders at `/order/create`
   - Search and add products
   - View order summary
   - Manage orders in admin panel (`/admin/orders`)

3. **Admin Panel**
   - Products management: `/admin/products`
   - Categories management: `/admin/categories`
   - Orders management: `/admin/orders`

## Running Tests

### Run All Tests

```bash
docker-compose exec app composer test
# Or
make test
```

### Run Module-Specific Tests

```bash
# Feature tests
docker-compose exec app vendor/bin/pest tests/Feature/

# Unit tests
docker-compose exec app vendor/bin/pest tests/Unit/
```

### Test-Specific Setup

Tests use an in-memory SQLite database by default. No additional setup needed.

## Architecture Overview

### Module Structure

The application uses **nwidart/laravel-modules** for a modular architecture:

```
Modules/
├── Catalog/              # Product & Category Management
│   ├── app/
│   │   ├── Models/       # Category, Product
│   │   ├── Filament/     # Admin resources
│   │   ├── Livewire/     # ProductCatalog component
│   │   └── Providers/    # Service providers
│   ├── database/migrations/
│   ├── routes/web.php
│   └── resources/views/
│
└── Order/                # Order Management
    ├── app/
    │   ├── Models/       # Order, OrderItem
    │   ├── Filament/     # Admin resources
    │   ├── Livewire/     # CreateOrder, ViewOrder
    │   └── Providers/    # Service providers
    ├── database/migrations/
    ├── routes/web.php
    └── resources/views/
```

### Cross-Module Communication Solution

**Challenge**: Order module needs product data but must remain independent of Catalog module.

**Solution**: Product snapshots with event-driven updates

1. **Product Snapshots**: Order items store complete product data (name, price) instead of foreign keys
2. **Repository Pattern**: Core app defines `ProductRepositoryInterface`
3. **Service Provider Binding**: Catalog module implements and binds the interface
4. **Loose Coupling**: Order module depends on interface, not Catalog implementation

```php
// app/Contracts/Repository/ProductRepositoryInterface.php
interface ProductRepositoryInterface {
    public function find(int $id): ?object;
    public function search(string $query): Collection;
}

// Catalog module implements
class ProductRepository implements ProductRepositoryInterface { ... }

// Catalog service provider binds
$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

// Order module uses via dependency injection
public function __construct(ProductRepositoryInterface $productRepo) { ... }
```

### Additional Patterns Implemented

1. **Repository Pattern**: Abstracts data access for Products and Categories
2. **Dependency Injection**: All cross-module dependencies via interfaces
3. **Service Providers**: Each module self-registers Livewire components

## 🚀 Features

### Core Stack
- **Laravel 12** with **PHP 8.3+**
- **PostgreSQL 16** database
- **Livewire 3** reactive components
- **Filament 4** admin panel
- **Pest 3** testing framework
- **Docker** containerized environment

### Code Quality Tools
- **Duster**: Multi-tool linting (TLint, PHP_CodeSniffer, PHP CS Fixer, Pint)
- **GitHub Actions**: Automated CI/CD with matrix testing (PHP 8.4)

```bash
# Check code style
make lint
docker-compose exec app composer lint

# Fix code style
make format
docker-compose exec app composer format
```

## 📝 Quick Reference

### Makefile Commands

```bash
make install      # Full installation
make up           # Start containers
make down         # Stop containers
make shell        # Access container shell
make migrate      # Run migrations
make fresh        # Fresh database with seed
make test         # Run tests
make lint         # Check code style
make format       # Fix code style
make quality      # Run all quality checks
```
