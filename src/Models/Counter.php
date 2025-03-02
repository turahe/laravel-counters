<?php

namespace Turahe\Counters\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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

    public function getTable()
    {
        return config('counter.models.table_name') ?: parent::getTable(); // TODO: Change the autogenerated stub
    }

    public function counterable(): MorphToMany
    {
        return $this->morphedByMany('counterable');

    }
}
