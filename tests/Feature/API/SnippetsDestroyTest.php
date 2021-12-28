<?php

namespace Tests\Feature\API;

use App\Snippet;
use App\User;
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
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
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
        $user1 = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $user2 = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet = factory(Snippet::class)->make();
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
        $user = factory(User::class)->create();
        $parent_snippet = factory(Snippet::class)->make();
        $child_snippet_1 = factory(Snippet::class)->make();
        $child_snippet_2 = factory(Snippet::class)->make();
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
        $fan = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippets = factory(Snippet::class, $snippets_starting_quantity)->make();
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
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
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
