<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function snippets()
    {
        return $this->belongsToMany(Snippet::class);
    }

    public function addSnippet(Snippet $snippet)
    {
        return $this->snippets()->save($snippet);
    }

}
