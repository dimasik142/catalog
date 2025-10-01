# Laravel Modular E-Commerce Platform

A modern Laravel 12 application featuring a modular architecture with Catalog and Order management modules, built with Filament 4, Livewire 3, and comprehensive code quality tools.

## 🚀 Features

### Core Stack
- **Laravel 12** - Latest PHP framework
- **PHP 8.3+** - Modern PHP (requires 8.3 or higher)
- **PostgreSQL 16** - Robust relational database
- **Livewire 3** - Reactive frontend components
- **Filament 4** - Powerful admin panel
- **Pest 3** - Modern testing framework
- **Docker** - Containerized development environment

### Modules

#### 📦 Catalog Module
- **Product Management**: CRUD operations with categories
- **Category System**: Hierarchical product organization
- **Public Browsing**: Livewire-powered catalog interface
- **Filament Admin**: Full-featured admin panels for products and categories
- **Features**:
  - Auto-slug generation for categories
  - Stock level tracking with color indicators
  - Category-based filtering
  - Product search functionality

#### 🛒 Order Module
- **Order Management**: Complete order lifecycle
- **Status Workflow**: pending → confirmed → shipped → delivered
- **Product Snapshots**: Independent from catalog changes
- **Filament Admin**: Order management with status transitions
- **Livewire Components**:
  - Order creation with product search
  - Order viewing and tracking
- **Architecture**: Fully decoupled from Catalog module

### Code Quality Tools
- **Duster**: Multi-tool linting (TLint, PHP_CodeSniffer, PHP CS Fixer, Pint)
- **Larastan**: PHPStan static analysis (Level 5)
- **GitHub Actions**: Automated CI/CD with matrix testing (PHP 8.3, 8.4)

## 📋 Prerequisites

- Docker & Docker Compose
- Git
- Make (optional, for Makefile commands)

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd testTask1
```

### 2. Start Docker Services

```bash
docker-compose up -d
```

### 3. Install Dependencies

```bash
docker-compose exec app composer install
```

### 4. Setup Environment

```bash
# Environment file already exists
# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate
```

### 5. Access the Application

- **Web Application**: http://localhost:8888
- **Filament Admin**: http://localhost:8888/admin
- **Catalog (Public)**: http://localhost:8888/catalog
- **Create Order**: http://localhost:8888/order/create

## 🎯 Quick Start with Makefile

If you have Make installed, use these convenient commands:

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
make phpstan      # Run static analysis
make quality      # Run all quality checks
```

## 📚 Usage

### Creating a Filament Admin User

```bash
docker-compose exec app php artisan make:filament-user
```

### Running Code Quality Checks

```bash
# Check code style
docker-compose exec app composer lint

# Fix code style issues
docker-compose exec app composer format

# Run static analysis
docker-compose exec app composer phpstan

# Run all tests
docker-compose exec app composer test
```

### Module Development

```bash
# Create a new module
docker-compose exec app php artisan module:make ModuleName

# Create model in module
docker-compose exec app php artisan module:make-model ModelName ModuleName

# Create migration in module
docker-compose exec app php artisan make:migration create_table_name --path=Modules/ModuleName/database/migrations
```

## 🏗️ Architecture

### Modular Structure

```
Modules/
├── Catalog/
│   ├── app/
│   │   ├── Models/           # Category, Product
│   │   ├── Filament/         # Admin resources
│   │   ├── Livewire/         # ProductCatalog component
│   │   └── Providers/        # Service providers
│   ├── database/
│   │   └── migrations/       # Module migrations
│   ├── routes/
│   │   └── web.php          # Module routes
│   └── resources/
│       └── views/           # Blade templates
│
└── Order/
    ├── app/
    │   ├── Models/          # Order, OrderItem
    │   ├── Filament/        # Admin resources
    │   ├── Livewire/        # CreateOrder, ViewOrder
    │   └── Providers/       # Service providers
    ├── database/
    │   └── migrations/      # Module migrations
    └── routes/
        └── web.php         # Module routes
```

### Key Design Decisions

- **Module Independence**: Order module stores product snapshots, not foreign keys
- **Service Providers**: Each module registers its own Livewire components
- **Filament Integration**: Centralized admin panel with module resources
- **Database Agnostic**: PostgreSQL in production, easily switchable

## 🧪 Testing

```bash
# Run all tests
docker-compose exec app composer test

# Run specific tests
docker-compose exec app vendor/bin/pest tests/Feature/
docker-compose exec app vendor/bin/pest tests/Unit/
```

## 🔍 Code Quality

### Linting

```bash
# Check for issues
docker-compose exec app composer lint

# Auto-fix issues
docker-compose exec app composer format
```

### Static Analysis

```bash
# Run PHPStan
docker-compose exec app composer phpstan

# Update baseline
docker-compose exec app composer phpstan:baseline
```

## 🚢 GitHub Actions CI

The project includes automated CI/CD:

- **Matrix Testing**: PHP 8.2, 8.3, 8.4
- **Database**: PostgreSQL 16
- **Steps**:
  1. Checkout code
  2. Setup PHP with extensions
  3. Install dependencies (with caching)
  4. Run migrations
  5. Lint code (Duster)
  6. Static analysis (PHPStan)
  7. Run tests (Pest)

## 📝 Available Commands

### Composer Scripts

```bash
composer lint         # Check code style
composer format       # Fix code style
composer phpstan      # Run static analysis
composer test         # Run tests
```

### Artisan Commands

```bash
php artisan module:list                    # List all modules
php artisan module:make ModuleName        # Create new module
php artisan module:make-model Model Module # Create model in module
```

## 📖 Documentation

- [Code Quality Guide](CODE_QUALITY.md) - Detailed guide for quality tools
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [nwidart/laravel-modules](https://nwidart.com/laravel-modules)

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
```bash
docker-compose ps
docker-compose up -d db
```

**Permission Denied**
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

**Composer Memory Issues**
```bash
docker-compose exec app php -d memory_limit=-1 /usr/local/bin/composer install
```

## 📜 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
