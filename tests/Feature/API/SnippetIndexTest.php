<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function see_minimum_text()
    {
        // Arrange
        User::factory()->create();
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function if_snippet_have_forks_user_can_see_them()
    {
        // Arrange
        $user = User::factory()->create();
        $parent_snippet = Snippet::factory()->make();
        $first_fork = Snippet::factory()->make([
            'title' => 'Title-1',
        ]);
        $second_fork = Snippet::factory()->make([
            'title' => 'Title-2',
        ]);
        $user->snippets()->save($parent_snippet);
        $parent_snippet->addFork($first_fork);
        $parent_snippet->addFork($second_fork);

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment($parent_snippet->fresh()->toArray())
            ->assertJsonFragment($first_fork->fresh()->toArray())
            ->assertJsonFragment($second_fork->fresh()->toArray());
    }

    /** @test */
    public function if_snippet_have_parent_user_can_see_him()
    {
        // Arrange
        $user = User::factory()->create();
        $parent_snippet = Snippet::factory()->make([
            'title' => 'Title-1',
            'description' => 'foo',
            'body' => 'bar',
        ]);
        $child_fork = Snippet::factory()->make(['fork_id' => $parent_snippet->id]);
        $user->snippets()->save($parent_snippet);
        $parent_snippet->addFork($child_fork);

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment($parent_snippet->fresh()->toArray())
            ->assertJsonFragment($child_fork->fresh()->toArray());
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
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(206)
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
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(206)
            ->assertSee('"data":[]', false);
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
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertDontSee('"data":[]')
            ->assertJsonFragment($snippet->fresh()->toArray());
    }

    /** @test */
    public function user_can_not_see_private_snippets_from_other_users()
    {
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $other_user_1 = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $other_user_2 = User::factory()->create(['api_token' => str_repeat('C', 60)]);
        $snippet_1 = Snippet::factory()->make([
            'public' => false,
        ]);
        $other_user_1->addSnippet($snippet_1);

        $snippet_2 = Snippet::factory()->make([
            'public' => false,
        ]);
        $snippet_3 = Snippet::factory()->make([
            'public' => false,
        ]);
        $other_user_2->addSnippet($snippet_2);
        $other_user_2->addSnippet($snippet_3);

        // Act
        $response = $this->apiRequest([
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertSee('"data":[]', false);
    }

}
