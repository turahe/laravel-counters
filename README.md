# Laravel Counters

[![Latest Stable Version](http://poser.pugx.org/turahe/laravel-counters/v)](https://packagist.org/packages/turahe/laravel-counters) 
[![Total Downloads](http://poser.pugx.org/turahe/laravel-counters/downloads)](https://packagist.org/packages/turahe/laravel-counters) 
[![Latest Unstable Version](http://poser.pugx.org/turahe/laravel-counters/v/unstable)](https://packagist.org/packages/turahe/laravel-counters) 
[![License](http://poser.pugx.org/turahe/laravel-counters/license)](https://packagist.org/packages/turahe/laravel-counters) 
[![PHP Version Require](http://poser.pugx.org/turahe/laravel-counters/require/php)](https://packagist.org/packages/turahe/laravel-counters)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/turahe/laravel-counters/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/turahe/laravel-counters/?branch=master)
[![PHP Composer](https://github.com/turahe/laravel-counters/actions/workflows/php.yml/badge.svg)](https://github.com/turahe/laravel-counters/actions/workflows/php.yml)

A flexible and powerful counter management system for Laravel applications. Easily track and manage various types of counters like page views, downloads, user actions, and more without cluttering your database schema.

## 🚀 Features

- **Model-specific counters**: Associate counters with any Eloquent model
- **Global counters**: System-wide counters for general statistics
- **Cookie-based tracking**: Prevent duplicate increments from the same user
- **Flexible configuration**: Customizable table names and settings
- **Artisan commands**: Create counters via command line
- **Laravel 10-12 support**: Compatible with modern Laravel versions
- **PHP 8.2+ support**: Built for modern PHP applications

## 📋 Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
  - [Model Counters](#model-counters)
  - [Global Counters](#global-counters)
  - [Cookie-based Tracking](#cookie-based-tracking)
- [API Reference](#api-reference)
- [Configuration](#configuration)
- [Testing](#testing)
- [Contributing](#contributing)

## Installation

### Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 10.x, 11.x, or 12.x

### Step-by-Step Installation

1. **Install the package via Composer:**

```bash
composer require turahe/laravel-counters
```

2. **Publish the configuration and migrations:**

```bash
php artisan vendor:publish --provider="Turahe\Counters\CountersServiceProvider"
```

This will publish:
- Configuration file: `config/counter.php`
- Migration file: `database/migrations/xxxx_xx_xx_xxxxxx_create_counters_tables.php`

3. **Run the migrations:**

```bash
php artisan migrate
```

This creates the `counters` and `counterables` tables in your database.

## Quick Start

### 1. Create a Counter

```php
use Turahe\Counters\Models\Counter;

// Create a counter for page views
Counter::create([
    'key' => 'page_views',
    'name' => 'Page Views',
    'initial_value' => 0,
    'step' => 1
]);
```

### 2. Use with Models

```php
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;
    
    // Your model code...
}
```

### 3. Track Views

```php
// In your controller
public function show(Post $post)
{
    $post->incrementCounter('page_views');
    
    return view('posts.show', compact('post'));
}
```

### 4. Global Counters

```php
use Turahe\Counters\Facades\Counters;

// Track total downloads
Counters::increment('total_downloads');
```

## Usage

### Model Counters

Add the `HasCounter` trait to any model you want to track:

```php
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;
    
    // Your model code...
}
```

#### Available Methods

```php
// Add a counter to a model
$post->addCounter('views');

// Get counter value
$views = $post->getCounterValue('views');

// Increment counter
$post->incrementCounter('views');

// Decrement counter
$post->decrementCounter('views', 2); // Decrement by 2

// Reset counter to initial value
$post->resetCounter('views');

// Remove counter from model
$post->removeCounter('views');

// Check if model has counter
if ($post->hasCounter('views')) {
    // Do something
}
```

### Global Counters

Use the `Counters` facade for system-wide counters:

```php
use Turahe\Counters\Facades\Counters;

// Create a counter
Counters::create('total_downloads', 'Total Downloads', 0, 1);

// Get counter value
$downloads = Counters::getValue('total_downloads');

// Increment counter
Counters::increment('total_downloads');

// Decrement counter
Counters::decrement('total_downloads', 2);

// Set specific value
Counters::setValue('total_downloads', 100);

// Reset to initial value
Counters::reset('total_downloads');
```

### Cookie-based Tracking

Prevent duplicate increments from the same user:

```php
// Only increment if user doesn't have cookie
Counters::incrementIfNotHasCookies('daily_visitors');
Counters::decrementIfNotHasCookies('available_slots');
```

## API Reference

### Model Methods (HasCounter Trait)

| Method | Description | Parameters |
|--------|-------------|------------|
| `addCounter($key, $initialValue = null)` | Add counter to model | `$key`: Counter key, `$initialValue`: Optional initial value |
| `getCounter($key)` | Get counter object | `$key`: Counter key |
| `getCounterValue($key)` | Get counter value | `$key`: Counter key |
| `hasCounter($key)` | Check if model has counter | `$key`: Counter key |
| `incrementCounter($key, $step = null)` | Increment counter | `$key`: Counter key, `$step`: Optional step value |
| `decrementCounter($key, $step = null)` | Decrement counter | `$key`: Counter key, `$step`: Optional step value |
| `resetCounter($key, $initialValue = null)` | Reset counter | `$key`: Counter key, `$initialValue`: Optional reset value |
| `removeCounter($key)` | Remove counter from model | `$key`: Counter key |

### Global Counter Methods (Counters Facade)

| Method | Description | Parameters |
|--------|-------------|------------|
| `create($key, $name, $initialValue = 0, $step = 1)` | Create a counter | `$key`: Counter key, `$name`: Display name, `$initialValue`: Initial value, `$step`: Step value |
| `get($key)` | Get counter object | `$key`: Counter key |
| `getValue($key, $default = null)` | Get counter value | `$key`: Counter key, `$default`: Default value if not found |
| `setValue($key, $value)` | Set counter value | `$key`: Counter key, `$value`: New value |
| `setStep($key, $step)` | Set counter step | `$key`: Counter key, `$step`: Step value |
| `increment($key, $step = null)` | Increment counter | `$key`: Counter key, `$step`: Optional step value |
| `decrement($key, $step = null)` | Decrement counter | `$key`: Counter key, `$step`: Optional step value |
| `reset($key)` | Reset counter | `$key`: Counter key |
| `incrementIfNotHasCookies($key)` | Increment if no cookie | `$key`: Counter key |
| `decrementIfNotHasCookies($key)` | Decrement if no cookie | `$key`: Counter key |

## Configuration

The package configuration is located at `config/counter.php`:

```php
return [
    'models' => [
        'counter' => Turahe\Counters\Models\Counter::class,
    ],

    'tables' => [
        'table_name' => 'counters',
        'table_pivot_name' => 'counterables',
    ],
    
    'database_connection' => env('COUNTER_DB_CONNECTION'),
];
```

### Customizing Table Names

You can customize the table names in the configuration:

```php
'tables' => [
    'table_name' => 'my_counters',
    'table_pivot_name' => 'my_counterables',
],
```

## Artisan Commands

### Create Counter

Create a counter via command line:

```bash
php artisan make:counter page_views "Page Views" 0 1
```

Parameters:
- `page_views`: Counter key
- `"Page Views"`: Display name
- `0`: Initial value
- `1`: Step value

## Testing

The package includes comprehensive tests. Run them with:

```bash
./vendor/bin/phpunit
```

### Test Coverage

- ✅ Model counter operations
- ✅ Global counter operations
- ✅ Cookie-based tracking
- ✅ Exception handling
- ✅ Database operations
- ✅ Configuration flexibility

## Common Usage Patterns

### ✅ Best Practices

1. **Always create counters before using them:**
```php
// Create the counter first
Counter::create([
    'key' => 'page_views',
    'name' => 'Page Views',
    'initial_value' => 0,
    'step' => 1
]);

// Then use it
$post->incrementCounter('page_views');
```

2. **Use meaningful counter keys:**
```php
// Good
$post->incrementCounter('article_views');
$user->incrementCounter('login_count');

// Avoid generic names
$post->incrementCounter('count');
```

3. **Handle counter existence gracefully:**
```php
if ($post->hasCounter('views')) {
    $post->incrementCounter('views');
} else {
    $post->addCounter('views');
    $post->incrementCounter('views');
}
```

### ❌ Common Mistakes

1. **Don't forget to run migrations:**
```bash
php artisan migrate
```

2. **Don't use counters without creating them first:**
```php
// This might fail if counter doesn't exist
$post->incrementCounter('undefined_counter');

// Better approach
$post->addCounter('new_counter');
$post->incrementCounter('new_counter');
```

## Database Seeding

Example seeder for creating counters:

```php
use Illuminate\Database\Seeder;
use Turahe\Counters\Models\Counter;

class CounterSeeder extends Seeder
{
    public function run()
    {
        // Create global counters
        Counter::create([
            'key' => 'total_downloads',
            'name' => 'Total Downloads',
            'initial_value' => 0,
            'step' => 1
        ]);

        Counter::create([
            'key' => 'daily_visitors',
            'name' => 'Daily Visitors',
            'initial_value' => 0,
            'step' => 1
        ]);

        // Create model-specific counters
        Counter::create([
            'key' => 'page_views',
            'name' => 'Page Views',
            'initial_value' => 0,
            'step' => 1
        ]);
    }
}
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

- **Issues**: [GitHub Issues](https://github.com/turahe/laravel-counters/issues)
- **Discussions**: [GitHub Discussions](https://github.com/turahe/laravel-counters/discussions)

---

Made with ❤️ by [Nur Wachid](https://www.wach.id)
