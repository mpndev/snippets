<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPrivateSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_can_see_his_private_snippets()
    {
        // Arrange
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $user->addSnippet(Snippet::factory()->make(['title' => '1foo', 'public' => true]));
        $user->addSnippet(Snippet::factory()->make(['title' => '2foo', 'public' => false]));
        $user->addSnippet(Snippet::factory()->make(['title' => '3bar', 'public' => true]));

        // Act
        $response = $this->apiRequest([
            'my-private-snippets',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonMissingExact(['title' => '1foo'])
            ->assertJsonFragment(['title' => '2foo'])
            ->assertJsonMissingExact(['title' => '3bar']);
    }
}
