<?php

namespace Tests\Feature\API;

use App\Models\Snippet;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteSnippetsDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.favorite.destroy';

    protected $test_verb = 'delete';

    /** @test */
    public function user_can_remove_snippet_from_favorite_snippets()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create();
        $user->addToFavoriteSnippets($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
    }

    /** @test */
    public function guest_cannot_remove_snippet_from_favorite_snippets()
    {
        // Arrange
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], []);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.',
                ],
            ]);
    }

}
