<?php

namespace App\Http\Controllers\API;

use App\Snippet;
use \Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Repositories\SnippetsRepository;
use Illuminate\Validation\ValidationException;

class SnippetsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'store',
            'update',
            'destroy',
        ]);
    }

    public function index()
    {
        $snippets = SnippetsRepository::getWithAllConstraints();

        return response()->json($snippets, 206);
    }

    public function show($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        if ((!$snippet->public && auth()->guard('api')->check() && $snippet->user_id != auth()->guard('api')->id()) || (!auth()->guard('api')->check() && !$snippet->public)) {
            return response()->json([
                'user' => [
                    'This action is unauthorized.'
                ],
            ], 403);
        }
        $snippet->load(['tags', 'user', 'parent', 'forks']);
        return response()->json($snippet->toArray(), 200);
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required|min:1|max:255|unique:snippets,title',
            'description' => 'sometimes|nullable|min:1|max:2000',
            'body' => 'required|min:1|max:100000',
            'settings' => 'sometimes|nullable|min: 1',
            'public' => 'boolean',
        ]);
        request()->merge(['description' => (request('description') === null ? '' : request('description'))]);

        $snippet = new Snippet(request([
            'title',
            'body',
            'description',
            'settings',
        ]));
        $snippet->public = request()->has('public') ? request('public') : false;
        $snippet->slug = Str::slug($snippet->title, '-');
        $snippet->fork_id = $this->getParentSnippetIdOnStore();
        auth()->user()->addSnippet($snippet);
        $snippet->actions()->create();

        $snippet->load(['tags', 'user', 'parent', 'forks']);

        return response()->json($snippet->toArray(), 201);
    }

    public function update($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        $this->authorize('update', $snippet);
        $this->validate(request(), [
            'title' => 'required|min:1|max:255|iunique:snippets,title,' . $snippet->id,
            'description' => 'sometimes|nullable|min:1|max:2000',
            'body' => 'required|min:1|max:100000',
            'settings' => 'sometimes|nullable|min: 1',
            'public' => 'boolean',
        ]);

        request()->merge([
            'description' => (request('description') === null ? '' : request('description')),
            'slug' => Str::slug(request('title'), '-'),
            'public' => request()->has('public') ? request('public') : false,
        ]);

        if ($snippet->have_parent) {
            $this->validateTitles($snippet->parent->title, $snippet->title);
        }

        $snippet->update(request([
            'title',
            'slug',
            'description',
            'body',
            'settings',
            'public',
        ]));

        $snippet->load(['tags', 'user', 'parent', 'forks']);

        return response()->json($snippet->toArray(), 200);
    }

    public function destroy($snippet_id_or_slug)
    {
        $snippet = Snippet::where('id', $snippet_id_or_slug)->orWhere('slug', $snippet_id_or_slug)->firstOrFail();
        $this->authorize('delete', $snippet);
        $snippet->delete();
        return response()->json(null,204);
    }

    protected function getParentSnippetIdOnStore()
    {
        if (request('_parent_id')) {
            $parent_id = request('_parent_id');
            $this->validateTitles(Snippet::find($parent_id)->title, request('title'));
            return $parent_id;
        }

        return null;
    }

    protected function validateTitles($parent_title, $child_title)
    {
        if ($parent_title === $child_title) {
            throw ValidationException::withMessages(['title' => 'Title cannot be the same as the forked snippet.']);
        }
    }

}
