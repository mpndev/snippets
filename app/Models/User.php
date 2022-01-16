<?php

namespace App\Models;

use App\Notifications\ResetpasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'id' => 'int',
    ];

    public function sendPasswordResetNotification($token) {
        $url = request()->getSchemeAndHttpHost() . '/password-reset?token=' . $token;
        $this->notify(new ResetpasswordNotification($url));
    }

    public function delete()
    {
        if (is_null($this->getKeyName())) {
            throw new \Exception('No primary key defined on model.');
        }
        if (! $this->exists) {
            return;
        }
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        foreach($this->snippets as $snippet) {
            $snippet->delete();
        }

        $this->touchOwners();
        $this->performDeleteOnModel();
        $this->fireModelEvent('deleted', false);

        return true;
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

    public function notPublicSnippets()
    {
        return $this->hasMany(Snippet::class)->where('public', false);
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

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function getRole($role) {
        return $this->roles()->whereIn('name', [$role])->first();
    }

    public function getAbility($ability) {
        return Ability::where('name', $ability)->first();
    }

    public function addRole($role) {
        if (is_string($role)) {
            if (strpos($role, ' ') !== false) {
                throw new \Exception("Role name cannot use spaces. Use underscores instead!");
            }
            $role = Role::firstOrNew([
                'name' => $role,
            ]);
            if (!$role->label) {
                $role->label = implode(' ', array_map(function ($word) {
                    return ucfirst($word);
                }, (explode('_', $role->name))));
                $role->save();
            }
        }
        $this->roles()->sync($role, false);
        return $this;
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    public function abilities() {
        if ($this->roles) {
            return $this->roles->map->abilities->flatten()->pluck('name')->unique()->toArray();
        }
        return [];
    }

    public function hasRole($looking_for_role = '') {
        foreach ($this->getRoles() as $role) {
            if ($role['name'] == $looking_for_role) {
                return true;
            }
        }
        return false;
    }

    public function hasAbility($looking_for_ability = '') {
        foreach ($this->abilities() as $ability) {
            if ($ability == $looking_for_ability) {
                return true;
            }
        }
        return false;
    }

}
