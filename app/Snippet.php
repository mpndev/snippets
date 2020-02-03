<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Snippet extends Model
{
    protected $guarded = [];
    public static $items_per_page = 5;

    public function forks()
    {
        return $this->hasMany(Snippet::class, 'fork_id');
    }

    public function parent()
    {
        return $this->belongsTo(Snippet::class, 'fork_id');
    }

    public function isAFork()
    {
        return !! $this->parent;
    }
}
