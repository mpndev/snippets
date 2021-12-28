<?php

namespace Tests\Feature\API;

use App\Tag;
use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagsIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.tags.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_can_get_all_tags()
    {
        // Arrange
        $snippet = factory(Snippet::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $snippet->addTag($tags[0]);
        $snippet->addTag($tags[1]);
        $snippet->addTag($tags[2]);

        // Act
        $response = $this->apiRequest();

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tags[0]->name])
            ->assertJsonFragment(['name' => $tags[1]->name])
            ->assertJsonFragment(['name' => $tags[2]->name]);
    }

    /** @test */
    public function guest_can_not_see_private_snippets_tags()
    {
        // Arrange
        $tag1 = factory(Tag::class)->create(['name' => 'foo']);
        $tag2 = factory(Tag::class)->create(['name' => 'bar']);
        $tag3 = factory(Tag::class)->create(['name' => 'foo bar']);

        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = factory(Snippet::class)->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = factory(Snippet::class)->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = factory(Snippet::class)->create();
        $snippet3->addTag($tag3);
        $another_user->addSnippet($snippet2);
        $another_user->addSnippet($snippet3);

        // Act
        $response = $this->apiRequest();

        // Assert
        $response->assertStatus(200)
            ->assertJsonMissingExact(['name' => $tag1->name])
            ->assertJsonMissingExact(['name' => $tag2->name])
            ->assertJsonFragment(['name' => $tag3->name]);
    }

    /** @test */
    public function user_can_not_see_private_snippets_tags_that_do_not_belongs_to_him()
    {
        // Arrange
        $tag1 = factory(Tag::class)->create(['name' => 'foo']);
        $tag2 = factory(Tag::class)->create(['name' => 'bar']);
        $tag3 = factory(Tag::class)->create(['name' => 'foo bar']);

        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = factory(Snippet::class)->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = factory(Snippet::class)->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = factory(Snippet::class)->create();
        $snippet3->addTag($tag3);
        $another_user->addSnippet($snippet2);
        $another_user->addSnippet($snippet3);

        // Act
        $response = $this->apiRequest([
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tag1->name])
            ->assertJsonMissingExact(['name' => $tag2->name])
            ->assertJsonFragment(['name' => $tag3->name]);
    }

    /** @test */
    public function user_can_see_his_private_snippets_tags()
    {
        // Arrange
        $tag1 = factory(Tag::class)->create(['name' => 'foo']);
        $tag2 = factory(Tag::class)->create(['name' => 'bar']);
        $tag3 = factory(Tag::class)->create(['name' => 'foo bar']);

        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = factory(Snippet::class)->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = factory(Snippet::class)->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = factory(Snippet::class)->create();
        $snippet3->addTag($tag3);
        $another_user->addSnippet($snippet2);
        $another_user->addSnippet($snippet3);

        // Act
        $response = $this->apiRequest([
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tag1->name])
            ->assertJsonMissingExact(['name' => $tag2->name])
            ->assertJsonFragment(['name' => $tag3->name]);
    }

}
