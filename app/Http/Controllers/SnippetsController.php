<?php

namespace App\Http\Controllers;

use App\Snippet;

class SnippetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'create',
            'store',
        ]);
    }

    public function index()
    {
        $snippets = Snippet::latest()->paginate(5);
        return view('snippets.index', compact('snippets'));
    }

    public function create()
    {
        return view('snippets.create');
    }

    public function show(Snippet $snippet)
    {
        return view('snippets.show', compact('snippet'));
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'body' => 'required',
        ]);

        Snippet::create([
            'title' => request('title'),
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        return redirect(route('snippets.index'));
    }
}
