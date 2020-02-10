<?php

namespace App\Http\Controllers;

use App\Snippet;
use App\User;

class UserFavoriteSnippetsController extends Controller
{
    public function index(User $user)
    {
        $snippets = $user->paginatedFavoriteSnippets();
        return view('user.snippets.index', compact('snippets'));
    }

    public function store(User $user, Snippet $snippet)
    {
        $user->addToFavoriteSnippets($snippet);
        return response()->json()->setStatusCode(200);
    }

    public function destroy(User $user, Snippet $snippet)
    {
        $user->removeFromFavoriteSnippets($snippet);
        return response()->json()->setStatusCode(200);
    }
}
