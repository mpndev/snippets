<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Snippet extends Model
{
    use Searchable;

    protected $appends = [
        'is_editing',
        'fans_quantity',
        'forks_quantity',
        'created_at_for_humans',
        'updated_at_for_humans',
    ];

    protected $guarded = [];

    public $searchable_fields = [
        [
            'name' => 'title',
            'priority' => 3,
        ],
        [
            'name' => 'description',
            'priority' => 2,
        ],
        [
            'name' => 'body',
            'priority' => 1,
        ],
    ];

    public function delete()
    {
        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }
        if (! $this->exists) {
            return;
        }
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        foreach($this->fans as $user) {
            $user->removeFromFavoriteSnippets($this);
        }
        foreach($this->tags as $tag) {
            $this->removeTag($tag);
        }
        $this->forks()->each(function($fork){
            $fork->update(['fork_id' => null]);
        });

        $this->touchOwners();
        $this->performDeleteOnModel();
        $this->fireModelEvent('deleted', false);

        return true;
    }

    public function forks()
    {
        return $this->hasMany(Snippet::class, 'fork_id')->with('forks');
    }

    public function parent()
    {
        return $this->belongsTo(Snippet::class, 'fork_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fans()
    {
        return $this->belongsToMany(User::class, 'favorite_snippets')->withTimestamps();
    }

    public function getFansQuantityAttribute()
    {
        return $this->fans()->count();
    }

    public function getForksQuantityAttribute()
    {
        return $this->forks()->count();
    }

    public function addFork(Snippet $fork)
    {
        $this->forks()->save($fork);
        return $this;
    }

    public function removeFork(Snippet $fork)
    {
        $fork->delete();
        return $this;
    }

    public function getIsForkAttribute()
    {
        return $this->parent()->exists();
    }

    public function getHaveForksAttribute()
    {
        return $this->forks()->exists();
    }

    public function getIsParentAttribute()
    {
        return $this->fork_id === null;
    }

    public function getIsEditingAttribute()
    {
        return false;
    }

    public function setIsEditingAttribute($value)
    {
        $this->is_editing = $value;
        return $this;
    }

    public function scopeOwned($query, $do_it = false, $id = null)
    {
        if ($do_it && $id) {
            $query = $query->Where('user_id', $id);
        }

        return $query;
    }

    public function scopeForked($query, $do_it = false, $id = null)
    {
        if ($do_it && $id) {
            $snippets = User::find($id)->snippets;
            $snippets_ids = [];
            foreach($snippets as $snippet) {
                if ($snippet->have_forks) {
                    $snippets_ids[] = $snippet->id;
                }
            }
            $query = $query->whereIn('id', $snippets_ids);
        }

        return $query;
    }

    public function scopeForks($query, $do_it = false, $id = null)
    {
        if ($do_it && $id) {
            $ids = [];
            $snippets = User::find($id)->snippets()->get();
            foreach($snippets as $snippet) {
                $ids[] = $snippet->id;
            }
            $query = $query->whereIn('fork_id', $ids);
        }

        return $query;
    }

    public function scopeFavorite($query, $do_it = false, $id = null)
    {
        if ($do_it && $id) {
            $ids = [];
            $snippets = User::find($id)->favoriteSnippets;
            foreach($snippets as $snippet) {
                $ids[] = $snippet->id;
            }

            $query = $query->whereIn('id', $ids);
        }

        return $query;
    }

    public function addTag(Tag $tag)
    {
        $this->tags()->save($tag);

        return $this;
    }

    public function removeTag(Tag $tag)
    {
        $this->tags()->detach($tag);
        if (!$tag->snippets->count()) {
            $tag->delete();
        }

        return $this;
    }

    public function haveTag(Tag $tag)
    {
        return $this->tags()->where('name', $tag->name)->exists();
    }

    public function scopeWithTags($query, $tags_names = false)
    {
        if ($tags_names) {
            $names = explode(',', str_replace(' ', '', $tags_names));
            $names = array_filter($names, function($name) {
                return !!strlen($name);
            });

            $query = $query->whereHas('tags', function($q) use ($names) {
                $q->whereIn('name', $names);
            }, '=', count($names));
        }

        return $query;
    }

    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedAtForHumansAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

}
