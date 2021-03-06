<?php

namespace Tests\Feature\API;

use App\Snippet;
use App\User;
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
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->create();

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
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
        $snippet = factory(Snippet::class)->create();

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
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
