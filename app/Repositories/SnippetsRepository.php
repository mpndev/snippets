<?php


namespace App\Repositories;

use App\Snippet;
use App\User;
use Illuminate\Support\Facades\Cache;

class SnippetsRepository
{
    public static function getWithAllConstraints()
    {
        $constraints = [
            'user_id' => request()->has('api_token') ? User::where('api_token', request('api_token'))->first()->id : null,
            'my_snippets' => request()->has('my-snippets'),
            'snippets_by_author' => request('snippets-by-author'),
            'snippets_created_at_the_same_day_as' => request('snippets-created-at-the-same-day-as'),
            'my_forked_snippets' => request()->has('my-forked-snippets'),
            'forks_of_my_snippets' => request()->has('forks-of-my-snippets'),
            'my_favorite_snippets' => request()->has('my-favorite-snippets'),
            'with_tags' => request()->get('with-tags'),
            'search' => request('search'),
            'latest' => request()->has('latest'),
            'most_liked_snippets' => request()->has('most-liked-snippets'),
            'most_copied_snippets' => request()->has('most-copied-snippets'),
            'limit' => request('limit'),
            'page' => request('page'),
        ];

        $cache_key = '';
        foreach ($constraints as $constraint_key => $constraint_value) {
            $cache_key .= $constraint_key . '->' . ($constraint_value == null ? 'null' : $constraint_value) . '|';
        }
        $cache_key = rtrim($cache_key, '|');

        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $snippets =  new Snippet;
        if ($constraints['my_snippets'] && $constraints['user_id']) {
            $snippets = $snippets->owned($constraints['user_id']);
        }

        if ($constraints['snippets_by_author']) {
            $snippets = $snippets->byAuthor($constraints['snippets_by_author']);
        }

        if ($constraints['snippets_created_at_the_same_day_as']) {
            $snippets = $snippets->byDay($constraints['snippets_created_at_the_same_day_as']);
        }

        $snippets = $snippets->search($constraints['search']);

        if ($constraints['with_tags']) {
            $snippets = $snippets->withTags($constraints['with_tags']);
        }

        if ($constraints['my_forked_snippets'] && $constraints['user_id']) {
            $snippets = $snippets->forked($constraints['user_id']);
        }

        if ($constraints['forks_of_my_snippets'] && $constraints['user_id']) {
            $snippets = $snippets->forks($constraints['user_id']);
        }

        if ($constraints['my_favorite_snippets']) {
            $snippets = $snippets->favorite($constraints['user_id']);
        }

        if ($constraints['most_liked_snippets']) {
            $snippets = $snippets->mostLikedSnippets();
        }

        if ($constraints['most_copied_snippets']) {
            $snippets = $snippets->mostCopiedSnippets();
        }

        $snippets = $snippets
            ->with('user')
            ->with('tags')
            ->orderBy('created_at', $constraints['latest'] ? 'desc' : 'asc')
            ->limit($constraints['limit'])
            ->get()
            ->orderByRelevance($constraints['search'])
            ->paginate(5)
            ->toArray();

        Cache::forever($cache_key, $snippets);

        return $snippets;
    }
}
