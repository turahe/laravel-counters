<?php

namespace Turahe\Counters\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Turahe\Counters\Models\Counterable
 *
 * @property int $id
 * @property string $counterable_type
 * @property int $counterable_id
 * @property int $counter_id
 * @property float $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Turahe\Counters\Models\Counter $counter
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereCounterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereCounterableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereCounterableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Counterable whereValue($value)
 * @mixin \Eloquent
 */
class Counterable extends Model
{
    protected $fillable = [
        'value',
        'counter_id',
        'counterable_id',
        'counterable_type',
    ];


    public function counter(){
        return $this->belongsTo(Counter::class);
    }
}
