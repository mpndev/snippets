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
}
