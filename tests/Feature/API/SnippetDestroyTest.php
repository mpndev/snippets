<?php

namespace Tests\Feature\API;

use App\Models\Snippet;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.destroy';

    protected $test_verb = 'delete';

    /** @test */
    public function user_can_delete_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('snippets', ['id' => $snippet->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_snippets()
    {
        // Arrange
        $user1 = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $user2 = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet = Snippet::factory()->make();
        $user1->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
            ], [
            'api_token' => $user2->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.'
                ]
            ]);
        $this->assertDatabaseHas('snippets', ['id' => $snippet->id]);
    }

    /** @test */
    public function when_is_deleted_children_snippets_become_parents()
    {
        // Arrange
        $user = User::factory()->create();
        $parent_snippet = Snippet::factory()->make();
        $child_snippet_1 = Snippet::factory()->make();
        $child_snippet_2 = Snippet::factory()->make();
        $user->addSnippet($parent_snippet);
        $parent_snippet->addFork($child_snippet_1);
        $parent_snippet->addFork($child_snippet_2);

        // Act
        $this->apiRequest([
            'snippet_id_or_slug' => $parent_snippet->id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $this->assertTrue($child_snippet_1->fresh()->is_parent);
        $this->assertTrue($child_snippet_2->fresh()->is_parent);
        $this->assertDatabaseMissing('snippets', ['id' => $parent_snippet->id]);
    }

    /** @test */
    public function when_snippet_is_deleted_users_that_have_the_snippet_to_favorite_do_not_have_the_snippet_in_favorite_anymore()
    {
        // Arrange
        $snippets_starting_quantity = 7;
        $expected_favorite_snippets_quantity = 6;
        $fan = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippets = Snippet::factory()->count($snippets_starting_quantity)->make();
        foreach($snippets as $snippet) {
            $user->addSnippet($snippet);
            $fan->addTofavoriteSnippets($snippet);
        }
        $snippet_id = $snippets[0]->id;

        // Act
        $this->apiRequest([
            'snippet_id_or_slug' => $snippet_id,
        ], [
            'api_token' => $user->api_token,
        ]);

        // Assert
        $this->assertSame($expected_favorite_snippets_quantity, $fan->favorite_snippets_quantity);
    }

    /** @test */
    public function guest_cannot_delete_snippets()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ]);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.'
                ]
            ]);
        $this->assertDatabaseHas('snippets', ['id' => $snippet->id]);
    }

}
