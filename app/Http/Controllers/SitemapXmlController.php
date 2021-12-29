<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Snippet;

class SitemapXmlController extends Controller
{
    public function index() {
        $snippets = Snippet::where('public', true)->get();
        $tags_last_modified = Tag::orderBy('updated_at','DESC')->first();
        return response()->view('sitemap.index', [
            'snippets' => $snippets,
            'tags_last_modified' => $tags_last_modified,
        ])->header('Content-Type', 'text/xml');
    }
}
