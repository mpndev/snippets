<?php

namespace Tests\Feature\API;

use App\Models\Tag;
use App\Models\User;
use App\Models\Snippet;
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
        $snippet = Snippet::factory()->create();
        $tags = Tag::factory()->count(3)->create();
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
    public function user_can_get_all_tags_sorted_alphabetically()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $tags = [
            Tag::factory()->create(['name' => 'Ffff']),
            Tag::factory()->create(['name' => 'Cccc']),
            Tag::factory()->create(['name' => 'Bbbb']),
            Tag::factory()->create(['name' => 'Eeee']),
            Tag::factory()->create(['name' => 'Aaaa']),
            Tag::factory()->create(['name' => 'Dddd']),
        ];
        $snippet->addTag($tags[0]);
        $snippet->addTag($tags[1]);
        $snippet->addTag($tags[2]);
        $snippet->addTag($tags[3]);
        $snippet->addTag($tags[4]);
        $snippet->addTag($tags[5]);

        // Act
        $response = $this->apiRequest();

        // Assert
        $response->assertStatus(200)
            ->assertSeeInOrder(['"A":', '"B":', '"C":', '"D":', '"E":', '"F":'], false);
    }

    /** @test */
    public function guest_can_not_see_private_snippets_tags()
    {
        // Arrange
        $tag1 = Tag::factory()->create(['name' => 'foo']);
        $tag2 = Tag::factory()->create(['name' => 'bar']);
        $tag3 = Tag::factory()->create(['name' => 'foo bar']);

        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = Snippet::factory()->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = Snippet::factory()->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = Snippet::factory()->create();
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
        $tag1 = Tag::factory()->create(['name' => 'foo']);
        $tag2 = Tag::factory()->create(['name' => 'bar']);
        $tag3 = Tag::factory()->create(['name' => 'foo bar']);

        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = Snippet::factory()->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = Snippet::factory()->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = Snippet::factory()->create();
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
        $tag1 = Tag::factory()->create(['name' => 'foo']);
        $tag2 = Tag::factory()->create(['name' => 'bar']);
        $tag3 = Tag::factory()->create(['name' => 'foo bar']);

        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $snippet1 = Snippet::factory()->create(['public' => false]);
        $snippet1->addTag($tag1);
        $user->addSnippet($snippet1);

        $another_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet2 = Snippet::factory()->create(['public' => false]);
        $snippet2->addTag($tag2);
        $snippet3 = Snippet::factory()->create();
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
