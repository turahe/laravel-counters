<?php

declare(strict_types=1);

namespace Turahe\Counters\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Optimized Counter model for PHP 8.4 and Laravel 11/12.
 */
class Counter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'name',
        'value',
        'initial_value',
        'step',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'value' => 'integer',
        'initial_value' => 'integer',
        'step' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'id',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return config('counter.tables.table_name', parent::getTable());
    }

    /**
     * Get the database connection for the model.
     */
    public function getConnectionName(): ?string
    {
        return config('counter.database_connection') ?? parent::getConnectionName();
    }

    /**
     * Get the counterable morph relationship.
     */
    public function counterable(): MorphToMany
    {
        return $this->morphedByMany(
            related: config('counter.models.counterable', Counterable::class),
            name: 'counterable',
            table: config('counter.tables.table_pivot_name', 'counterables')
        )->withPivot('value')
          ->withTimestamps();
    }

    /**
     * Scope a query to only include counters with a specific key.
     */
    public function scopeByKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    /**
     * Scope a query to only include counters with a specific name.
     */
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope a query to only include counters with a value greater than the given value.
     */
    public function scopeWithValueGreaterThan(Builder $query, int $value): Builder
    {
        return $query->where('value', '>', $value);
    }

    /**
     * Scope a query to only include counters with a value less than the given value.
     */
    public function scopeWithValueLessThan(Builder $query, int $value): Builder
    {
        return $query->where('value', '<', $value);
    }

    /**
     * Scope a query to only include active counters (value > 0).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('value', '>', 0);
    }

    /**
     * Scope a query to only include inactive counters (value = 0).
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('value', '=', 0);
    }

    /**
     * Get the counter's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->key;
    }

    /**
     * Check if the counter is active.
     */
    public function isActive(): bool
    {
        return $this->value > 0;
    }

    /**
     * Check if the counter is inactive.
     */
    public function isInactive(): bool
    {
        return $this->value === 0;
    }

    /**
     * Get the percentage change from initial value.
     */
    public function getPercentageChangeAttribute(): float
    {
        if ($this->initial_value === 0) {
            return $this->value > 0 ? 100.0 : 0.0;
        }

        return (($this->value - $this->initial_value) / $this->initial_value) * 100;
    }

    /**
     * Get the formatted value with thousands separator.
     */
    public function getFormattedValueAttribute(): string
    {
        return number_format($this->value);
    }

    /**
     * Boot the model and register any model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when counter is updated or deleted
        static::updated(function (Counter $counter) {
            $counter->clearCache();
        });

        static::deleted(function (Counter $counter) {
            $counter->clearCache();
        });
    }

    /**
     * Clear the cache for this counter.
     */
    private function clearCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget("counters:{$this->key}");
    }
}
