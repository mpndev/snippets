<?php

namespace Tests\Feature;

use App\Snippet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteSnippetsPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_add_snippet_to_favorite_snippets_page()
    {
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->create());

        $response = $this->post(route('favorite-snippets.store', [
            'user' => $user->name,
            'snippet' => $snippet->id,
        ]));

        $response->assertStatus(302);
        $this->assertDatabaseHas('favorite_snippets', ['user_id' => $user->id, 'snippet_id' => $user->favoriteSnippets->first()->id]);
    }

    /** @test */
    public function user_can_remove_snippet_from_favorite_snippets_page()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->create());
        $user->addToFavoriteSnippets($snippet);
        $this->assertDatabaseHas('favorite_snippets', ['user_id' => $user->id, 'snippet_id' => $user->favoriteSnippets->first()->id]);

        $response = $this->delete(route('favorite-snippets.destroy', [
            'user' => $user->name,
            'snippet' => $snippet->id,
        ]));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('favorite_snippets', ['user_id' => $user->id, 'snippet_id' => $snippet->id]);
    }
}
