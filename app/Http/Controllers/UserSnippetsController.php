<?php

namespace App\Http\Controllers;

use App\Snippet;
use App\User;

class UserSnippetsController extends Controller
{
    public function index(User $user)
    {
        $snippets = $user->snippets()->latest()->paginate();
        return view('user.snippets.index', compact('snippets'));
    }
}