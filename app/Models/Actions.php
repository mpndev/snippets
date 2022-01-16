<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actions extends Model
{
    use HasFactory;

    protected $table = 'actions';

    protected $guarded = [];
}
