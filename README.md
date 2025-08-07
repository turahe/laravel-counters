# Laravel Counters

A modern, optimized counter management package for Laravel 11/12 with PHP 8.4 support.

## Features

- ✅ **PHP 8.4 Optimized**: Uses readonly properties, constructor property promotion, match expressions, and improved type declarations
- ✅ **Laravel 11/12 Compatible**: Modern service provider patterns and dependency injection
- ✅ **High Performance**: Built-in caching, bulk operations, and optimized database queries
- ✅ **Type Safe**: Full type declarations and strict typing throughout
- ✅ **Modern Patterns**: Uses modern PHP and Laravel patterns and best practices
- ✅ **Comprehensive Testing**: Full test coverage with modern testing practices

## Installation

```bash
composer require turahe/laravel-counters
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=counters-config
```

## Usage

### Basic Counter Operations

```php
use Turahe\Counters\Facades\Counters;

// Create a counter
$counter = Counters::create('visitors', 'Page Visitors', 0, 1);

// Increment counter
Counters::increment('visitors');

// Get counter value
$value = Counters::getValue('visitors');

// Decrement counter
Counters::decrement('visitors');

// Reset counter to initial value
Counters::reset('visitors');
```

### Advanced Features

```php
// Bulk operations
$results = Counters::bulkIncrement(['counter1', 'counter2', 'counter3'], 2);

// Search counters
$counters = Counters::getAll('search_term');

// Get statistics
$stats = Counters::getStats();

// Cookie-based increment (prevents duplicate counts)
Counters::incrementIfNotHasCookies('unique_visitors');
```

### Using with Models

Add the `HasCounter` trait to your models:

```php
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;
    
    // Your model code...
}
```

Then use the counter methods:

```php
$post = Post::find(1);

// Add a counter to the post
$post->addCounter('views');

// Increment the counter
$post->incrementCounter('views');

// Get the counter value
$views = $post->getCounterValue('views');

// Bulk operations
$post->bulkIncrementCounters(['views', 'likes', 'shares']);
```

## Configuration Options

The package supports extensive configuration through environment variables:

```env
# Database
COUNTER_TABLE_NAME=counters
COUNTER_PIVOT_TABLE_NAME=counterables
COUNTER_DB_CONNECTION=mysql

# Cache
COUNTER_CACHE_ENABLED=true
COUNTER_CACHE_PREFIX=counters:
COUNTER_CACHE_TTL=3600

# Cookies
COUNTER_COOKIE_PREFIX=counters-cookie-
COUNTER_COOKIE_LIFETIME=525600

# Defaults
COUNTER_DEFAULT_INITIAL_VALUE=0
COUNTER_DEFAULT_STEP=1

# Performance
COUNTER_BULK_OPERATIONS=true
COUNTER_MAX_BULK_SIZE=100
COUNTER_QUERY_TIMEOUT=30
```

## Artisan Commands

Create a new counter:

```bash
php artisan make:counter visitors "Page Visitors" --initial-value=0 --step=1 --notes="Track page visitors"
```

## Performance Optimizations

### Caching
The package includes built-in caching for counter lookups, significantly improving performance for frequently accessed counters.

### Bulk Operations
Use bulk operations to update multiple counters efficiently:

```php
// Bulk increment
$results = Counters::bulkIncrement(['counter1', 'counter2'], 5);

// Bulk decrement
$results = Counters::bulkDecrement(['counter1', 'counter2'], 3);
```

### Database Indexes
The migration includes optimized indexes for better query performance.

## Testing

Run the test suite:

```bash
composer test
```

## PHP 8.4 Features Used

- **Readonly Properties**: Immutable data structures
- **Constructor Property Promotion**: Cleaner class definitions
- **Match Expressions**: Modern control flow
- **Named Arguments**: Self-documenting function calls
- **Improved Type Declarations**: Better type safety
- **Strict Types**: Enforced type checking

## Laravel 11/12 Features Used

- **Modern Service Providers**: Deferrable providers for better performance
- **Improved Dependency Injection**: Constructor injection and type hints
- **Enhanced Model Features**: Better relationships and scopes
- **Modern Command Structure**: Improved Artisan commands

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
