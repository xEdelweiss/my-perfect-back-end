<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
