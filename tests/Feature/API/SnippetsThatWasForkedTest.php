<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetsThatWasForkedTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function users_can_see_only_snippets_that_was_forked()
    {
        // Arrange
        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $other_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet1 = factory(Snippet::class)->make(['title' => 'foo']);
        $snippet2 = factory(Snippet::class)->make(['title' => 'bar']);
        $snippet3 = factory(Snippet::class)->make(['title' => 'baz']);
        $user->addSnippet($snippet1);
        $user->addSnippet($snippet2);
        $user->addSnippet($snippet3);
        $user->snippets[0]->addFork(factory(Snippet::class)->create(['user_id' => $other_user->id]));
        $user->snippets[1]->addFork(factory(Snippet::class)->create(['user_id' => $other_user->id]));

        // Act
        $response = $this->apiRequest([
            'my-forked-snippets',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['title' => 'foo'])
            ->assertJsonFragment(['title' => 'bar']);
    }

}
