<?php

namespace App\Http\Controllers;

use App\Snippet;
use Illuminate\Http\Request;

class UserSnippetsController extends Controller
{
    public function index()
    {
        $snippets = Snippet::where('user_id', auth()->id())->latest()->paginate();
        return view('user.snippets.index', compact('snippets'));
    }
}
