<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    public function paginatedForkedSnippets($perPage = 5)
    {
        $paginator = $this->snippets()->latest()->paginate($perPage);
        $paginator->setCollection($paginator->getCollection()->filter(function($snippet) {
            return $snippet->haveForks();
        }));
        return $paginator;
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function addToFavoriteSnippets($snippet)
    {
        $this->favoriteSnippets()->attach($snippet->id);
        return $snippet;
    }

    public function removeFromFavoriteSnippets($snippet)
    {
        $this->favoriteSnippets()->detach($snippet->id);
        return $snippet;
    }

    public function favoriteSnippets()
    {
        return $this->belongsToMany(Snippet::class, 'favorite_snippets')->withTimestamps();
    }

    public function paginatedFavoriteSnippets($perPage = 5)
    {
        return $this->favoriteSnippets()->latest()->paginate($perPage);
    }

}
