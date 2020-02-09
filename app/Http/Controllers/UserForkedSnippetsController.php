<?php

namespace App\Http\Controllers;

use App\User;

class UserForkedSnippetsController extends Controller
{
    public function index(User $user)
    {
        $snippets = $user->paginatedForkedSnippets();
        return view('user.snippets.index', compact('snippets'));
    }
}
