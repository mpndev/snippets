<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Snippet;

class FavoriteSnippetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'destroy',
            'store',
        ]);
    }

    public function store(Snippet $snippet)
    {
        auth()->user()->addToFavoriteSnippets($snippet);
        return response()->json($snippet->toArray(), 201);
    }

    public function destroy(Snippet $snippet)
    {
        auth()->user()->removeFromFavoriteSnippets($snippet);
        return response()->json(null, 204);
    }
}
