<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Counters\Traits\HasCounter;

class Post extends Model
{
    use HasCounter;

    protected $fillable = ['name'];
}
