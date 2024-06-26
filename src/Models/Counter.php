<?php
namespace Turahe\Counters\Models;

use Turahe\Counters\Facades\Counters;
use Illuminate\Database\Eloquent\Model;

/**
 * Turahe\Counters\Models\Counter
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property float $initial_value
 * @property float $value
 * @property float $step
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Counter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Counter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Counter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereInitialValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counter whereValue($value)
 * @mixin \Eloquent
 */
class Counter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'name',
        'value',
        'initial_value',
        'step',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('counter.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('counter.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * @return mixed
     */
    public function getIncrementUrl()
    {
        return Counters::getIncrementUrl($this->key);
    }

    /**
     * @return mixed
     */
    public function getDecrementUrl()
    {
        return Counters::getDecrementUrl($this->key);
    }
}
