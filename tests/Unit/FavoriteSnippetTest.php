<?php

namespace Tests\Unit;

use App\Snippet;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteSnippetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function snippet_can_be_added_to_favorite_snippets()
    {
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());

        $user->addToFavoriteSnippets($snippet);

        $this->assertEquals(1, $user->favoriteSnippets()->count());
    }

    /** @test */
    public function snippet_can_be_removed_from_favorite_snippets()
    {
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());

        $user->removeFromFavoriteSnippets($snippet);

        $this->assertEquals(0, $user->favoriteSnippets()->count());
    }
}
