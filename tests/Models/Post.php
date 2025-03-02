<?php

namespace Turahe\Counters\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
