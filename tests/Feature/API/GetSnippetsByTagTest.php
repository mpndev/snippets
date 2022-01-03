<?php

namespace Tests\Feature\API;

use App\Tag;
use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSnippetsByTagsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function see_only_snippets_for_specific_tags()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet1 = Snippet::factory()->make(['title' => 'fuzz']);
        $snippet2 = Snippet::factory()->make(['title' => 'buzz']);
        $snippet3 = Snippet::factory()->make(['title' => 'braz']);
        $snippet4 = Snippet::factory()->make(['title' => 'fraz']);
        $snippet5 = Snippet::factory()->make(['title' => 'farz']);
        $tag1 = Tag::factory()->make(['name' => 'foo']);
        $tag2 = Tag::factory()->make(['name' => 'faz']);
        $tag3 = Tag::factory()->make(['name' => 'bar']);
        $tag4 = Tag::factory()->make(['name' => 'baz']);
        $user->addSnippet($snippet1);
        $user->addSnippet($snippet2);
        $user->addSnippet($snippet3);
        $user->addSnippet($snippet4);
        $user->addSnippet($snippet5);
        $snippet1->addTag($tag1);
        $snippet1->addTag($tag2);
        $snippet1->addTag($tag3);
        $snippet2->addTag($tag1);
        $snippet2->addTag($tag2);
        $snippet3->addTag($tag1);
        $snippet3->addTag($tag2);
        $snippet3->addTag($tag3);
        $snippet4->addTag($tag1);
        $snippet5->addTag($tag4);

        // Act
        $response = $this->apiRequest([
            'with-tags' => $tag1->name . ',' . $tag2->name . ',' . $tag3->name,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['title' => 'fuzz'])
            ->assertJsonFragment(['title' => 'braz'])
            ->assertJsonMissingExact(['title' => 'buzz'])
            ->assertJsonMissingExact(['title' => 'fraz'])
            ->assertJsonMissingExact(['title' => 'farz']);
    }

}
