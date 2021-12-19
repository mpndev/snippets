<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetShowTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.show';

    protected $test_verb = 'get';

    /** @test */
    public function guest_can_see_single_snippet_with_all_useful_fields()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function guest_can_see_public_snippets()
    {
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make([
            'public' => true,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertDontSee('"data":[]')
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function guest_can_not_see_private_snippets()
    {
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make([
            'public' => false,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id
        ]);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.'
                ]
            ]);
    }

    /** @test */
    public function author_can_see_his_private_snippets()
    {
        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $snippet = factory(Snippet::class)->make([
            'public' => false,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertDontSee('"data":[]')
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function user_can_not_see_private_snippets_from_other_users()
    {
        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $other_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet = factory(Snippet::class)->make([
            'public' => false,
        ]);
        $other_user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.'
                ]
            ]);
    }

}
