<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_can_see_hes_snippets()
    {
        // Arrange
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $user->addSnippet(Snippet::factory()->make(['title' => '1foo']));
        $user->addSnippet(Snippet::factory()->make(['title' => '2foo']));
        $user->addSnippet(Snippet::factory()->make(['title' => '3foo']));
        $other_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $other_user->addSnippet(Snippet::factory()->make(['title' => '1bar']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '2bar']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '3bar']));
        $other_user->addSnippet(Snippet::factory()->make(['title' => '4bar']));

        // Act
        $response = $this->apiRequest([
            'my-snippets',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['title' => '1foo'])
            ->assertJsonFragment(['title' => '2foo'])
            ->assertJsonFragment(['title' => '3foo'])
            ->assertJsonMissingExact(['title' => '1bar'])
            ->assertJsonMissingExact(['title' => '2bar'])
            ->assertJsonMissingExact(['title' => '3bar'])
            ->assertJsonMissingExact(['title' => '4bar']);
    }

}
