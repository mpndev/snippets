<?php

namespace App\Http\Controllers\API;

use App\Tag;
use App\User;
use App\Snippet;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        });
        return response()->json($tags, 200);
    }

    public function store()
    {
        $this->validate(request(), [
            'snippet' => 'exists:snippets,id',
        ]);

        $snippet = Snippet::find(request('snippet'));
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
        $this->validate(request(), [
            'snippet' => 'exists:snippets,id',
        ]);

        $snippet = Snippet::find(request('snippet'));
        $snippet->removeTag($tag);

        return response()->json(null, 204);
    }
}
