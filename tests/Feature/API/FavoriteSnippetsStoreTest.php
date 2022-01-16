<?php

namespace Tests\Feature\API;

use App\Models\Snippet;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteSnippetsStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.favorite.store';

    protected $test_verb = 'post';

    /** @test */
    public function user_can_add_snippet_to_favorite()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJson($snippet->toArray());
    }

    /** @test */
    public function user_can_add_snippet_to_favorite_with_slug()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJson($snippet->toArray());
    }

    /** @test */
    public function guest_cannot_add_snippets_to_favorite()
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
