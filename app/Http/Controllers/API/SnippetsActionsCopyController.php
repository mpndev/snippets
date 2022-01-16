<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Snippet;

class SnippetsActionsCopyController extends Controller
{
    public function update($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        $snippet->copy();
        return response()->json($snippet->fresh()->toArray(), 202);
    }
}
