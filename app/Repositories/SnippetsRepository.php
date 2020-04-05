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
        $my_forked_snippets = request()->has('my-forked-snippets');
        $forks_of_my_snippets = request()->has('forks-of-my-snippets');
        $my_favorite_snippets = request()->has('my-favorite-snippets');
        $with_tags = request()->get('with-tags');
        $search = request('search');
        $latest = request()->has('latest');

        $snippets =  Snippet
            ::owned($my_snippets, $user_id)
            ->search($search)
            ->withTags($with_tags)
            ->forked($my_forked_snippets, $user_id)
            ->forks($forks_of_my_snippets, $user_id)
            ->favorite($my_favorite_snippets, $user_id)
            ->with('user')
            ->with('tags')
            ->orderBy('created_at', $latest ? 'desc' : 'asc')
            ->get()
            ->orderByRelevance($search)
            ->paginate(5);

        return $snippets;
    }
}
