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
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function guest_can_see_public_snippets()
    {
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make([
            'public' => true,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id
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
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make([
            'public' => false,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id
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
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $snippet = Snippet::factory()->make([
            'public' => false,
        ]);
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
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
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $other_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet = Snippet::factory()->make([
            'public' => false,
        ]);
        $other_user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
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

    /** @test */
    public function guest_can_see_single_snippet_by_the_slug_of_the_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->slug
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

}
