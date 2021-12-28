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

    public function store($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        auth()->user()->addToFavoriteSnippets($snippet);
        return response()->json($snippet->toArray(), 201);
    }

    public function destroy($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        auth()->user()->removeFromFavoriteSnippets($snippet);
        return response()->json(null, 204);
    }
}
