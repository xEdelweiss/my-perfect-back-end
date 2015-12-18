<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Group
 *
 * @property integer $id
 * @property integer $owner_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group whereUpdatedAt($value)
 */
class Group extends Model
{
    /**
     * Get the owner of this group.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all of the users for the group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
