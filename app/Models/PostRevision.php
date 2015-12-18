<?php

namespace App\Models;

/**
 * App\Models\PostRevision
 *
 * @property-read Post $original
 * @property-read User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conner\Tagging\Model\Tagged[] $tagged
 * @property-read mixed $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\$revisionClass[] $revisions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post withAllTags($tagNames)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post withAnyTag($tagNames)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Post findByRequest($request = null)
 * @property integer $id
 * @property string $title
 * @property string $intro
 * @property string $text
 * @property integer $author_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $base_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereIntro($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereAuthorId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereBaseId($value)
 * @property boolean $is_draft
 * @property boolean $is_private
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereIsDraft($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PostRevision whereIsPrivate($value)
 */
class PostRevision extends Post
{
    protected $skipRevisioning = true;

    /**
     * Get Question whose revision this is
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(Post::class, 'base_id');
    }
}
