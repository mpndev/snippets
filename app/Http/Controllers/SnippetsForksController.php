<?php

namespace App\Http\Controllers;

use App\Snippet;

class SnippetsForksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'create',
            'store',
        ]);
    }

    public function create(Snippet $snippet)
    {
        return view('snippets.forks.create', compact('snippet'));
    }

    public function store(Snippet $snippet)
    {
        $this->validate(request(), [
            'title' => 'required',
            'body' => 'required',
        ]);

        Snippet::create([
            'title' => request('title'),
            'body' => request('body'),
            'fork_id' => $snippet->id,
            'user_id' => auth()->id(),
        ]);

        return redirect(route('snippets.index'));
    }
}
