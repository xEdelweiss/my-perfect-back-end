<?php

namespace App\Models;

use App\Traits\Model\FindByRequestTrait;
use Conner\Tagging\Taggable;
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
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post findByRequest($request = null)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conner\Tagging\Model\Tagged[] $tagged
 * @property-read mixed $tags
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post withAllTags($tagNames)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post withAnyTag($tagNames)
 */
class Post extends Model
{
    use Taggable;
    use FindByRequestTrait;

    /**
     * The attributes that can be used to filter table entities.
     *
     * @var array
     */
    protected $findable = ['id', 'author_id', 'tag'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'intro', 'text'];

    /**
     * Get the author of this post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
