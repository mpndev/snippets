<?php

namespace Tests\Feature\API;

use App\Models\Tag;
use App\Models\User;
use App\Models\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagCanBeAddedToSnippetTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.tags.store';

    protected $test_verb = 'post';

    /** @test */
    public function snippet_can_add_tag_to_himself()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'name' => 'foo',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $tag = Tag::find($snippet->tags()->first()->id);
        $this->assertEquals('foo', $tag->name);
        $response
            ->assertStatus(200)
            ->assertJson($tag->toArray());
    }

    /** @test */
    public function snippet_cannot_add_tag_to_himself_if_already_tag_exist()
    {
        // Arrange
        $existing_tag = Tag::factory()->create(['name' => 'foo']);
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);
        $user->snippets[0]->addTag($existing_tag);

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'name' => 'foo',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $tag = Tag::find($snippet->tags()->first()->id);
        $this->assertEquals('foo', $tag->name);
        $response
            ->assertStatus(200)
            ->assertJson($tag->toArray());
    }

    /** @test */
    public function tag_cannot_be_added_not_existing_snippet()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => 1,
        ], [
            'name' => 'foo',
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
    public function guest_cannot_add_tags_to_snippets()
    {
        // Arrange
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ], [
            'name' => 'foo',
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

}
