<?php
namespace Turahe\Counters\Tests\Models;

use Turahe\Counters\Traits\HasCounter;
use Illuminate\Database\Eloquent\Model;

/**
 * Turahe\Counters\Tests\Models\Post
 *
 * @property int $id
 * @property int|null $category_id
 * @property int $user_id
 * @property string $slug
 * @property string $title
 * @property string|null $subtitle subtitle of title post
 * @property string|null $description description of post
 * @property string $content_raw
 * @property string $content_html
 * @property string $type
 * @property int $is_sticky
 * @property string|null $published_at
 * @property string|null $layout
 * @property int|null $record_left
 * @property int|null $record_right
 * @property int|null $record_dept
 * @property int|null $record_ordering
 * @property int|null $parent_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Turahe\Counters\Models\Counter[] $counters
 * @property-read int|null $counters_count
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContentRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIsSticky($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRecordDept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRecordLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRecordOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRecordRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @mixin \Eloquent
 */
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
