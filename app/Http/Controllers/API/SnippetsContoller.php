<?php

namespace App\Http\Controllers\API;

use App\Snippet;
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

        return response()->json($snippets->toArray(), 206);
    }

    public function show(Snippet $snippet)
    {
        return response()->json($snippet->toArray(), 200);
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required|min:1|max:255',
            'description' => 'sometimes|nullable|min:1|max:2000',
            'body' => 'required|min:1|max:100000',
        ]);

        $snippet = new Snippet(request([
            'title',
            'body',
            'description',
        ]));
        $snippet->fork_id = $this->getParentSnippetIdOnStore();
        auth()->user()->addSnippet($snippet);

        return response()->json($snippet->fresh()->toArray(), 201);
    }

    public function update(Snippet $snippet)
    {
        $this->authorize('update', $snippet);
        $this->validate(request(), [
            'title' => 'required|min:1|max:255',
            'description' => 'sometimes|nullable|min:1|max:2000',
            'body' => 'required|min:1|max:100000',
        ]);

        if ($snippet->have_parent) {
            $this->validateTitles($snippet->parent->title, $snippet->title);
        }

        $snippet->update(request([
            'title',
            'description',
            'body',
        ]));

        return response()->json($snippet->fresh()->toArray(), 200);
    }

    public function destroy(Snippet $snippet)
    {
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
