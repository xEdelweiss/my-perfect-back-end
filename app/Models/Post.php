<?php

namespace App\Models;

use App\Traits\Model\FindByRequestTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Post
 *
 * @property integer $id
 * @property string $title
 * @property string $intro
 * @property string $text
 * @property integer $author_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereIntro($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereAuthorId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post whereUpdatedAt($value)
 * @property-read User $author
 */
class Post extends Model
{
    use FindByRequestTrait;

    protected $findable = ['id', 'author_id'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
