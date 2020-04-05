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
        factory(User::class)->create();
        $snippet = factory(Snippet::class)->create();

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
        $user = factory(User::class)->create();
        $parent_snippet = factory(Snippet::class)->make();
        $first_fork = factory(Snippet::class)->make([
            'title' => 'Title-1',
        ]);
        $second_fork = factory(Snippet::class)->make([
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
        $user = factory(User::class)->create();
        $parent_snippet = factory(Snippet::class)->make([
            'title' => 'Title-1',
            'description' => 'foo',
            'body' => 'bar',
        ]);
        $child_fork = factory(Snippet::class)->make(['fork_id' => $parent_snippet->id]);
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

}
