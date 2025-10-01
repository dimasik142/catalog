# Laravel Docker Setup - Quick Reference

## Overview
This is a complete Laravel 12 application running on Docker with PHP 8.4, PostgreSQL 16, and Nginx.

## What Was Created

### Docker Configuration Files
- **Dockerfile** - PHP 8.4-FPM container with all Laravel dependencies
- **docker-compose.yml** - Multi-container setup (PHP, PostgreSQL, Nginx)
- **docker/nginx/nginx.conf** - Nginx web server configuration
- **.dockerignore** - Excludes unnecessary files from Docker build

### Laravel Application
- **Laravel 12.32.5** (latest version)
- **PostgreSQL 16** database configured and connected
- All migrations run successfully
- Environment configured for Docker networking

## Services Running

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| PHP 8.4 | laravel-app | 9000 | PHP-FPM application server |
| Nginx | laravel-nginx | 8888 | Web server (http://localhost:8888) |
| PostgreSQL 16 | laravel-postgres | 5432 | Database server |

## How to Start the Project

### Start all containers
```bash
docker-compose up -d
```

### Stop all containers
```bash
docker-compose down
```

### Stop and remove volumes (WARNING: deletes database data)
```bash
docker-compose down -v
```

## Access the Application

### Web Browser
Open your browser and navigate to:
```
http://localhost:8888
```

You should see the Laravel welcome page.

### Check Container Status
```bash
docker-compose ps
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

## Database Connection Details

### From Host Machine
- **Host**: localhost
- **Port**: 5432
- **Database**: laravel
- **Username**: laravel
- **Password**: secret

### From Laravel Container (already configured in .env)
- **Host**: db
- **Port**: 5432
- **Database**: laravel
- **Username**: laravel
- **Password**: secret

### Connect to PostgreSQL CLI
```bash
docker-compose exec db psql -U laravel -d laravel
```

## Common Laravel Commands

### Run Artisan Commands
```bash
docker-compose exec app php artisan [command]
```

Examples:
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Check migration status
docker-compose exec app php artisan migrate:status

# Create a new migration
docker-compose exec app php artisan make:migration create_example_table

# Run database seeder
docker-compose exec app php artisan db:seed

# Clear cache
docker-compose exec app php artisan cache:clear

# Generate application key
docker-compose exec app php artisan key:generate
```

### Run Composer Commands
```bash
docker-compose exec app composer [command]
```

Examples:
```bash
# Install dependencies
docker-compose exec app composer install

# Update dependencies
docker-compose exec app composer update

# Add a package
docker-compose exec app composer require vendor/package
```

### Access Container Shell
```bash
docker-compose exec app bash
```

## File Permissions

The application files are owned by `www-data` user inside the container. If you need to fix permissions:

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/bootstrap/cache
```

## Troubleshooting

### Container won't start
```bash
# Check logs
docker-compose logs -f

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database connection issues
```bash
# Check if database is running
docker-compose ps

# Check database logs
docker-compose logs db

# Verify .env configuration
docker-compose exec app cat .env | grep DB_
```

### Port already in use
If port 8888 is already in use, edit `docker-compose.yml` and change:
```yaml
ports:
  - "8888:80"
```
to another port like:
```yaml
ports:
  - "9000:80"
```
Then restart:
```bash
docker-compose down
docker-compose up -d
```

### Permission denied errors
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

## Development Workflow

1. Make changes to your Laravel code
2. Changes are automatically reflected (volume mounted)
3. For configuration changes, restart containers:
   ```bash
   docker-compose restart
   ```

## Production Notes

For production deployment, you should:
1. Change database credentials in `docker-compose.yml` and `.env`
2. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
3. Use proper SSL/TLS certificates
4. Configure Nginx for production (gzip, caching, etc.)
5. Use a production-grade database backup strategy
6. Implement proper logging and monitoring

## Additional Setup Steps

### Install Frontend Dependencies (if needed)
```bash
docker-compose exec app npm install
docker-compose exec app npm run build
```

### Run Tests
```bash
docker-compose exec app php artisan test
```

## Network Information

All containers are connected via the `laravel-network` bridge network, allowing them to communicate using service names (app, db, nginx).

## Volume Mounts

- Application code: `./` mapped to `/var/www/html`
- PostgreSQL data: Named volume `postgres-data` for persistence

## Tech Stack

- **PHP**: 8.4-fpm
- **Laravel**: 12.32.5
- **PostgreSQL**: 16
- **Nginx**: Alpine (latest)
- **Composer**: Latest

## Support

For Laravel documentation: https://laravel.com/docs
For Docker Compose documentation: https://docs.docker.com/compose/
