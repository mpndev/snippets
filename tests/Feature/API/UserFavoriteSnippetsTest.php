<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFavoriteSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_can_see_hes_favorite_snippets()
    {
        // Arrange
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $other_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $other_user->addSnippet(Snippet::factory()->make(['title' => '1foo']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '2foo']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '3bar']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '1fuzz']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '2foob']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '3barf']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '4barf']));
        $snippets = $other_user->snippets()->get();
        $user->addToFavoriteSnippets($snippets[0]);
        $user->addToFavoriteSnippets($snippets[1]);
        $user->addToFavoriteSnippets($snippets[2]);
        $user->addToFavoriteSnippets($snippets[3]);

        // Act
        $response = $this->apiRequest([
            'my-favorite-snippets',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['title' => '1foo'])
            ->assertJsonFragment(['title' => '2foo'])
            ->assertJsonFragment(['title' => '3bar'])
            ->assertJsonFragment(['title' => '1fuzz'])
            ->assertJsonMissingExact(['title' => '2foob'])
            ->assertJsonMissingExact(['title' => '3barf'])
            ->assertJsonMissingExact(['title' => '4barf']);
    }
}
