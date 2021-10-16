# Counters Management for laravel project.

[![Latest Stable Version](http://poser.pugx.org/turahe/laravel-counters/v)](https://packagist.org/packages/turahe/laravel-counters) 
[![Total Downloads](http://poser.pugx.org/turahe/laravel-counters/downloads)](https://packagist.org/packages/turahe/laravel-counters) 
[![Latest Unstable Version](http://poser.pugx.org/turahe/laravel-counters/v/unstable)](https://packagist.org/packages/turahe/laravel-counters) 
[![License](http://poser.pugx.org/turahe/laravel-counters/license)](https://packagist.org/packages/turahe/laravel-counters) 
[![PHP Version Require](http://poser.pugx.org/turahe/laravel-counters/require/php)](https://packagist.org/packages/turahe/laravel-counters)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/turahe/laravel-counters/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/turahe/laravel-counters/?branch=master)
[![PHP Composer](https://github.com/turahe/laravel-counters/actions/workflows/php.yml/badge.svg)](https://github.com/turahe/laravel-counters/actions/workflows/php.yml)

* [Installation](#installation)
* [Usage](#usage)
  * [1) Using Counters Associated with model](#1-using-counters-with-no-models)
  * [2) Using Counters with no models](#2-using-counters-with-no-models)
  * [3) Using artisan commands](#3-using-artisan-commands)
* [Database Seeding](#database-seeding)

In some cases, you need to manage the state of the counters in your laravel project, like the number of visitors of your website,
or number of view for a post, or number of downloads for a file, this needs to create a new table to save these records,
or at least adding new column for your tables to save the count value.  
Therefore, with this package, you can create as many counters for your model without needing to create a physical column in your model's table.
Moreover, you can store public counters like "number_of_downloads" for your website, without needing to create a whole table to store it.




In summary, this package allows you to manage counters in your laravel project.

Once installed you can do stuff like this:

```php

//increment/decrement system counters
Counters::increment('number_of_downloads'); 

// increment/decrement model objects counters
$post->incrementCounter('number_of_views');
$feature->decrementCounter('number_of_bugs');
$user->decrementCounter('balance', 2); // decrement the balance of the user by 2
```
There are many other methods that are mentioned below.

## Installation


### Laravel

This package can be used in Laravel 5.4 or higher. If you are using an older version of Laravel You can install the package via composer:

``` bash
composer require turahe/laravel-counters
```

In Laravel 5.5 and higher versions, the service provider will automatically get registered. In older versions of the framework just add the service provider in `config/app.php` file:

```php
'providers' => [
    // ...
    Turahe\Counters\CountersServiceProvider::class,
    //...
];
```

You must publish [the migration](https://github.com/turahe/laravel-counters/tree/master/database/migrations) with:

```bash
php artisan vendor:publish --provider="Turahe\Counters\CountersServiceProvider" --tag="migrations"
```

After the migration has been published you can create the tables by running the migrations:

```bash
php artisan migrate
```


## Usage

### 1) Using Counters with no models
First, add the `Turahe\Counters\Traits\HasCounter` trait to your  model(s):
for example we can add it to Post Model
```php
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;

    // ...
}
```

This package allows the posts to be associated with counters. Every post can associated with multiple counters.


We can create a counter like this, for example, lets create a counter for the number of views for the Post Model.
```php
use Turahe\Counters\Models\Counter;

$counter = Counter([
            'key' => 'number_of_views',
            'name' => 'Views',
            'initial_value' => 0 //(could be left empty, default value is 0)
            'step' => 1 // (could be left empty, default value is 1)
        ]);
```


After that, for example, in the show function of post controller, you can add this line:
```php
class PostsController extends Controller
{
    public function show(Post $post)
    {
        //...
        $post->incrementCounter('number_of_views');
        //...
    }
}
```
By doing this, the counter of number_of_views for every post will be incremented [by the step size] as we show the post.

This package has another functions.
```php
// will return the counter object
$post->getCounter($key); 

// will return the counter value
$post->getCounterValue($key);

//will add record in countrable table for this post object
$post->addCounter($key);

//will remove the record from countrable table for this post object.
$post->removeCounter($key);

//increment the counter with the given $key
//Note that this will create record in countrable table,if it's not exist
//if $step is entered, it will increment with the value of $step
$post->incrementCounter($key, $step = null);

//decrement the counter with the given $key
//Note that this will create record in countrable table,if it's not exist
//if $step is entered, it will decrement with the value of $step
$post->decrementCounter($key, $step = null);

 // will reset the counter value (to initial_value) for the post object.
$post->resetCounter($key);
```


### 2) Using Counters with no models.
Sometimes, you have general counters that are not associated with any models, for example the number visitor for your website.

Therefore, this package will allow you to deal with Counter with these types.

```php

use Turahe\Counters\Facades\Counters; 
class Test 
{
    public function incrementFunction()
    {
        //moreover you can add this function in your public page to be incremented 
        //every time user hits your website
        Counters::increment('number_of_downloads');
    }
}
```

This Facade has many other functions:

```php
// will return the counter object
Counters::get($key); 

// will return the counter value
Counters::getValue($key, $default = null); 

// set the value of the counter
Counters::setValue($key, $value);

//set the step of the counter
Counters::setStep($key, $step);

//increment the counter with the given $key
Counters::increment($key, $step = null);

//decrement the counter with the given $key
Counters::decrement($key, $step = null);

 // will reset the counter for the inital_value
Counters::reset($key);
```

In some cases, you want to increment the counter once for every person, for example no need to increment the number_of_downloads counter every time the same user refreshes the page.

So you can use these functions:

```php
Counters::incrementIfNotHasCookies($key);
Counters::decrementIfNotHasCookies($key);
```


## 3) Using artisan commands

You can create a Counter from a console with artisan commands.
The following command creates the counter number_of_downloads with initial value 0 and step 1
```bash
php artisan make:counter number_of_downloads Visitors 0 1
```

## Database Seeding

Here's a sample seeder.

```php
    use Illuminate\Database\Seeder;
    use Turahe\Counters\Facades\Counters;

    class CounterTableSeeder extends Seeder
    {
        public function run()
        {

            // create Counters
            //This will create a counter with inital value as 3, and every increment 5 will be added.
            Counter::create([
                'key' => 'number_of_downloads',
                'name' => 'Visitors',
                'initial_value' => 3,
                'step' => 5
            ]);
            //This counter will has 0 as inital_value and 1 as step
            Counter::create([
                'key' => 'number_of_downloads2',
                'name' => 'Visitors2'
            ]);

            $viewCounter = Counter::create([
                'key' => 'number_of_views',
                'name' => 'Views'
            ]);
            
            $post = Post::find(1);
            $post->addCounter('number_of_views');// to add the record to countrable table
            
            
        }
    }
```
