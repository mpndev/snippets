<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Snippet;

class SnippetsActionsCopyController extends Controller
{
    public function update(Snippet $snippet)
    {
        $snippet->copy();
        return response()->json($snippet->fresh()->toArray(), 202);
    }
}
