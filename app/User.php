<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    public function addSnippet($snippet)
    {
        $this->snippets()->save($snippet);
        return $this;
    }

    public function addToFavoriteSnippets($snippet)
    {
        $this->favoriteSnippets()->attach($snippet->id);
        return $this;
    }

    public function removeFromFavoriteSnippets($snippet)
    {
        $this->favoriteSnippets()->detach($snippet->id);
        return $this;
    }

    public function removeSnippet($snippet)
    {
        $this->snippets()->find($snippet->id)->delete();
        return $this;
    }

    public function favoriteSnippets()
    {
        return $this->belongsToMany(Snippet::class, 'favorite_snippets')->withTimestamps();
    }

    public function isSnippetFavorite($snippet)
    {
        return $this->favoriteSnippets()->where('snippet_id', $snippet->id)->exists();
    }

    public function getSnippetsQuantityAttribute()
    {
        return $this->snippets()->count();
    }

    public function getFavoriteSnippetsQuantityAttribute()
    {
        return $this->favoriteSnippets()->count();
    }

    public function getLogoutPathAttribute()
    {
        return route('login.destroy');
    }

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    public function copy(Snippet $snippet)
    {
        $snippet->copy();
        return $this;
    }

}
