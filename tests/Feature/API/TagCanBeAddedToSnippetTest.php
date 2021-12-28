<?php

namespace Tests\Feature\API;

use App\Tag;
use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITagsCanBeAddedToSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.tags.store';

    protected $test_verb = 'post';

    /** @test */
    public function snippet_can_add_tag_to_himself()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
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
        $existing_tag = factory(Tag::class)->create(['name' => 'foo']);
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
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
        $user = factory(User::class)->create();

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
        $snippet = factory(Snippet::class)->create();

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
