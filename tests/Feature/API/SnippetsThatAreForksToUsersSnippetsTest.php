<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetsThatAreForksToUsersSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function users_can_see_snippets_that_forks_his_snippets()
    {
        // Arrange
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $other_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet1 = Snippet::factory()->make(['description' => 'foo']);
        $snippet2 = Snippet::factory()->make(['description' => 'bar']);
        $snippet3 = Snippet::factory()->make(['description' => 'baz']);
        $user->addSnippet($snippet1);
        $user->addSnippet($snippet2);
        $user->addSnippet($snippet3);
        $fork1 = Snippet::factory()->make(['description' => 'fazz']);
        $fork2 = Snippet::factory()->make(['description' => 'fuzz']);
        $other_user->addSnippet($fork1);
        $other_user->addSnippet($fork2);
        $user->snippets[0]->addFork($fork1);
        $user->snippets[1]->addFork($fork2);

        // Act
        $response = $this->apiRequest([
            'forks-of-my-snippets',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertJsonFragment($fork1->fresh()->toArray())
            ->assertJsonFragment($fork2->fresh()->toArray())
            ->assertJsonMissingExact($snippet1->fresh()->toArray())
            ->assertJsonMissingExact($snippet2->fresh()->toArray())
            ->assertJsonMissingExact($snippet3->fresh()->toArray());
    }
}
