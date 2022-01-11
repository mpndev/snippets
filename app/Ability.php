<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

}