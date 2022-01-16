<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use App\Models\Snippet;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TagsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'store',
            'destroy'
        ]);
    }

    public function index()
    {
        $snippets = Snippet::visibility(Auth::guard('api')->check() ? Auth::guard('api')->id() : null)->with('tags')->get();
        $tags = $snippets->map(function($snippet, $key) {
            return $snippet->tags;
        })->collapse();
        $tags = $tags->unique('name')->groupBy(function($tag) {
            return substr($tag->name, 0, 1);
        })->sortKeys();
        return response()->json($tags, 200);
    }

    public function store()
    {
        $snippet_id_or_slug = request('snippet_id_or_slug');
        if (!$snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->first()) {
            throw ValidationException::withMessages([
                'snippet_id_or_slug' => 'The selected snippet id or slug is invalid.',
            ]);
        }
        $tag_name = request('name');

        if (!$tag = Tag::where('name', $tag_name)->first()) {
            $tag = new Tag(request(['name']));
        }

        if (!$snippet->haveTag($tag)) {
            $snippet->addTag($tag);
        }

        return response()->json($tag->toArray(), 200);
    }

    public function destroy(Tag $tag)
    {
        $snippet_id_or_slug = request('snippet_id_or_slug');
        if (!$snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->first()) {
            throw ValidationException::withMessages([
                'snippet_id_or_slug' => 'The selected snippet id or slug is invalid.',
            ]);
        }

        $snippet->removeTag($tag);

        return response()->json(null, 204);
    }
}
