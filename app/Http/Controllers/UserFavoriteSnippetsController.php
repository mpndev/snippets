<?php

namespace App\Http\Controllers;

use App\Snippet;
use App\User;
use Illuminate\Http\Request;

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
        return redirect()->back();
    }

    public function destroy(User $user, Snippet $snippet)
    {
        $user->removeFromFavoriteSnippets($snippet);
        return redirect()->back();
    }
}
