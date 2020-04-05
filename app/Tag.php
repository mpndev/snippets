<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
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
