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
