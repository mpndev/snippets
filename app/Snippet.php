<?php

namespace App;

use App\Traits\Searchable;
use App\Actions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
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
        'times_copied',
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

    public function flushCache()
    {
        Event::listen('eloquent.updated: App\Snippet', function() {
            Cache::flush();
        });
    }

    public function save(array $options = [])
    {
        $query = $this->newModelQuery();

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = $this->isDirty() ?
                $this->performUpdate($query) : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);

            if (! $this->getConnectionName() &&
                $connection = $query->getConnection()) {
                $this->setConnection($connection->getName());
            }
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        $this->flushCache();

        return $saved;
    }

    public function update(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        $this->flushCache();

        return $this->fill($attributes)->save($options);
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
        $this->flushCache();

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

    public function actions()
    {
        return $this->hasOne(Actions::class);
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

    public function scopeOwned($query, $id)
    {
        return $query->Where('user_id', $id);
    }

    public function scopeForked($query, $id)
    {
        $snippets = User::find($id)->snippets;
        $snippets_ids = [];
        foreach($snippets as $snippet) {
            if ($snippet->have_forks) {
                $snippets_ids[] = $snippet->id;
            }
        }
        return $query = $query->whereIn('id', $snippets_ids);
    }

    public function scopeForks($query, $id)
    {
        $ids = [];
        $snippets = User::find($id)->snippets()->get();
        foreach($snippets as $snippet) {
            $ids[] = $snippet->id;
        }
        return $query = $query->whereIn('fork_id', $ids);
    }

    public function scopeFavorite($query, $id)
    {
        $ids = [];
        $snippets = User::find($id)->favoriteSnippets;
        foreach($snippets as $snippet) {
            $ids[] = $snippet->id;
        }

        return $query = $query->whereIn('id', $ids);
    }

    public function scopeMostLikedSnippets($query)
    {
        return $query = $query->withCount('fans')->orderBy('fans_count', 'desc');
    }

    public function scopeMostCopiedSnippets($query)
    {
        return $query->orderByDesc(function($q) {
            $q->select('times_copied')
                ->from('actions')
                ->orderBy('times_copied', 'desc')
                ->whereColumn('snippet_id', 'snippets.id');
        });
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

    public function scopeWithTags($query, $tags_names)
    {
        $names = explode(',', str_replace(' ', '', $tags_names));
        $names = array_filter($names, function($name) {
            return !!strlen($name);
        });

        return $query = $query->whereHas('tags', function($q) use ($names) {
            $q->whereIn('name', $names);
        }, '=', count($names));
    }

    public function scopeByAuthor($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeByDay($query, $id)
    {
        $constrain_snippet = Snippet::find($id);
        return $query = $query->whereDate('created_at', $constrain_snippet->created_at);
    }

    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedAtForHumansAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    public function getTimesCopiedAttribute()
    {
        if ($this->actions()->first()) {
            return $this->actions()->first()->times_copied;
        }
        return 0;
    }

    public function copy()
    {
        if ($this->actions === null) {
            $this->actions()->create();
        }
        $this->actions()->update(['times_copied' => $this->fresh()->actions->times_copied + 1]);

        return $this->fresh();
    }

}
