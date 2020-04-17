<?php


namespace App\Repositories;

use App\Snippet;
use App\User;

class SnippetsRepository
{
    public static function getWithAllConstraints()
    {
        $user_id = request()->has('api_token') ? User::where('api_token', request('api_token'))->first()->id : null;
        $my_snippets = request()->has('my-snippets');
        $snippets_by_author = request('snippets-by-author');
        $snippets_created_at_the_same_day_as = request('snippets-created-at-the-same-day-as');
        $my_forked_snippets = request()->has('my-forked-snippets');
        $forks_of_my_snippets = request()->has('forks-of-my-snippets');
        $my_favorite_snippets = request()->has('my-favorite-snippets');
        $with_tags = request()->get('with-tags');
        $search = request('search');
        $latest = request()->has('latest');
        $most_liked_snippets = request()->has('most-liked-snippets');
        $most_copied_snippets = request()->has('most-copied-snippets');
        $limit = request('limit');

        $snippets =  Snippet
            ::owned($my_snippets, $user_id)
            ->byAuthor($snippets_by_author)
            ->byDay($snippets_created_at_the_same_day_as)
            ->search($search)
            ->withTags($with_tags)
            ->forked($my_forked_snippets, $user_id)
            ->forks($forks_of_my_snippets, $user_id)
            ->favorite($my_favorite_snippets, $user_id)
            ->mostLikedSnippets($most_liked_snippets)
            ->mostCopiedSnippets($most_copied_snippets)
            ->with('user')
            ->with('tags')
            ->orderBy('created_at', $latest ? 'desc' : 'asc')
            ->limit($limit)
            ->get()
            ->orderByRelevance($search)
            ->paginate(5);

        return $snippets;
    }
}
