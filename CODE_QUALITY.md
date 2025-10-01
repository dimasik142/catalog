# Code Quality Tools

This project uses several code quality tools to ensure consistent code style and catch potential issues.

## Tools Installed

### 1. Laravel Duster
**Purpose**: Multi-tool code quality checker (combines TLint, PHP_CodeSniffer, PHP CS Fixer, and Pint)

**Usage**:
```bash
# Check for code style issues (lint)
composer lint

# Automatically fix code style issues
composer format

# Inside Docker
docker-compose exec app composer lint
docker-compose exec app composer format
```

### 2. Larastan (PHPStan for Laravel)
**Purpose**: Static analysis tool to find bugs without running code
**Level**: 5 (moderate strictness)
**Baseline**: Generated with 57 existing errors (to be fixed incrementally)

**Usage**:
```bash
# Run static analysis
composer phpstan

# Regenerate baseline (after fixing errors)
composer phpstan:baseline

# Inside Docker
docker-compose exec app composer phpstan
docker-compose exec app composer phpstan:baseline
```

**Configuration**: `phpstan.neon`
- Analyzes: `app/` and `Modules/` directories
- Excludes: database, vendor, storage, bootstrap/cache
- Baseline: `phpstan-baseline.neon`

### 3. Pest
**Purpose**: Modern PHP testing framework

**Usage**:
```bash
# Run all tests
composer test

# Inside Docker
docker-compose exec app composer test
docker-compose exec app vendor/bin/pest
```

## GitHub Actions CI

The project includes a GitHub Actions workflow (`.github/workflows/ci.yml`) that runs on every push and pull request.

**Matrix Testing**:
- PHP versions: 8.2, 8.3, 8.4
- Database: PostgreSQL 16

**Workflow Steps**:
1. Setup PHP with required extensions
2. Install Composer dependencies (with caching)
3. Setup Laravel environment
4. Run database migrations
5. **Run Duster Lint** (`composer lint`)
6. **Run PHPStan** (`composer phpstan`)
7. **Run Pest Tests** (`composer test`)

## Available Composer Scripts

```json
{
  "lint": "vendor/bin/duster lint",
  "format": "vendor/bin/duster fix",
  "phpstan": "vendor/bin/phpstan analyse --memory-limit=512M",
  "phpstan:baseline": "vendor/bin/phpstan analyse --generate-baseline --memory-limit=512M",
  "test": "@php artisan test"
}
```

## Quick Commands

```bash
# Check everything (locally)
composer lint && composer phpstan && composer test

# Format code and test
composer format && composer test

# Inside Docker - Full quality check
docker-compose exec app bash -c "composer lint && composer phpstan && composer test"

# Inside Docker - Format and verify
docker-compose exec app bash -c "composer format && composer lint && composer phpstan && composer test"
```

## Pre-commit Recommendations

Before committing code, run:
```bash
docker-compose exec app composer format
docker-compose exec app composer phpstan
docker-compose exec app composer test
```

Or create a git pre-commit hook to automate this.

## Continuous Improvement

1. **Baseline Reduction**: Periodically work on reducing the PHPStan baseline errors
2. **Coverage**: Add more tests to improve code coverage
3. **Strictness**: Gradually increase PHPStan level as code quality improves
4. **Custom Rules**: Add project-specific linting rules to Duster configuration

## Files Modified/Created

- ✅ `composer.json` - Added dev dependencies and scripts
- ✅ `phpstan.neon` - PHPStan configuration
- ✅ `phpstan-baseline.neon` - Baseline with 57 existing errors
- ✅ `.github/workflows/ci.yml` - GitHub Actions workflow
- ✅ All code formatted with Duster

## Resources

- [Laravel Duster](https://github.com/tighten/duster)
- [Larastan](https://github.com/larastan/larastan)
- [PHPStan](https://phpstan.org/)
- [Pest](https://pestphp.com/)
