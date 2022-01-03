<?php

namespace Tests\Feature\API;

use App\Tag;
use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagCanBeDeletedForSnippetTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.tags.destroy';

    protected $test_verb = 'delete';

    /** @test */
    public function tag_can_be_removed_from_target_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $tag = Tag::factory()->make(['name' => 'foo']);
        $user->addSnippet($snippet);
        $snippet->addTag($tag);

        // Act
        $response = $this->apiRequest([
            'tag' => $tag->id,
        ], [
            'snippet_id_or_slug' => $snippet->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertEquals(0, $tag->snippets->count());
    }

    /** @test */
    public function guest_cannot_remove_tag_for_any_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $tag = Tag::factory()->make(['name' => 'foo']);
        $user->addSnippet($snippet);
        $snippet->addTag($tag);

        // Act
        $response = $this->apiRequest([
            'tag' => $tag->id,
        ], [
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
    }

    /** @test */
    public function tag_cannot_be_deleted_for_unexisting_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $tag = Tag::factory()->make(['name' => 'foo']);
        $user->addSnippet($snippet);
        $snippet->addTag($tag);

        // Act
        $response = $this->apiRequest([
            'tag' => $tag->id,
        ], [
            'snippet_id_or_slug' => '2',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'snippet_id_or_slug' => [
                    'The selected snippet id or slug is invalid.',
                ]
            ]);
    }

    /** @test */
    public function unexisting_tag_cannot_be_removed_from_target_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'tag' => '1',
        ], [
            'snippet_id_or_slug' => $snippet->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(404)
            ->assertJson([
                'tag' => [
                    'Tag not found.'
                ],
            ]);
    }

    /** @test */
    public function tag_will_not_be_deleted_if_target_snippet_remove_it_and_no_one_use_it()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet1 = Snippet::factory()->make();
        $snippet2 = Snippet::factory()->make();
        $tag = Tag::factory()->make(['name' => 'foo']);
        $user->addSnippet($snippet1);
        $user->addSnippet($snippet2);
        $snippet1->addTag($tag);
        $snippet2->addTag($tag);

        // Act
        $response = $this->apiRequest([
            'tag' => $tag->id,
        ], [
            'snippet_id_or_slug' => $snippet1->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertEquals(1, $tag->snippets->count());
    }

    /** @test */
    public function tag_will_be_deleted_if_every_snippet_remove_it()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet1 = Snippet::factory()->make();
        $snippet2 = Snippet::factory()->make();
        $tag = Tag::factory()->make(['name' => 'foo']);
        $user->addSnippet($snippet1);
        $user->addSnippet($snippet2);
        $snippet1->addTag($tag);
        $snippet2->addTag($tag);

        // Act
        $this->actingAs($user)->apiRequest([
            'tag' => $tag->id,
        ], [
            'snippet_id_or_slug' => $snippet1->id,
            'api_token' => $user->api_token,
        ]);
        $this->apiRequest([
            'tag' => $tag->id,
        ], [
            'snippet_id_or_slug' => $snippet2->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('snippet_tag', [
            'snippet_id' => $snippet1->id,
            'tag_id' => $tag->id,
        ]);
        $this->assertDatabaseMissing('snippet_tag', [
            'snippet_id' => $snippet2->id,
            'tag_id' => $tag->id,
        ]);
        $this->assertDatabaseHas('snippets', ['id' => $snippet1->id]);
        $this->assertDatabaseHas('snippets', ['id' => $snippet2->id]);
    }

}
