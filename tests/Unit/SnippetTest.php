<?php

namespace Tests\Unit;

use App\Tag;
use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_forks()
    {
        // Arrange
        $expected_forks_amount = 7;
        $expected_total_snippets_amount = $expected_forks_amount + 1;
        $parent_snippet = Snippet::factory()->create();
        $children = [];
        foreach (range(1, $expected_forks_amount) as $item) {
            $children[] = Snippet::factory()->create(['fork_id' => $parent_snippet->id]);
        }

        // Act
        $forks = $parent_snippet->forks;

        // Assert
        $this->assertEquals($expected_total_snippets_amount, Snippet::count());
        $this->assertEquals($expected_forks_amount, $forks->count());
        $forks->each(function($fork, $index) use ($children) {
            $this->assertEquals($fork->id, $children[$index]->id);
        });
    }

    /** @test */
    public function it_can_get_his_parent()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->create(['fork_id' => $snippet->id]);

        // Act
        $parent = $fork->parent;

        // Assert
        $this->assertEquals($snippet->id, $parent->id);
    }

    /** @test */
    public function it_can_get_escaped_body()
    {
        // Arrange
        $code = '<p>lorem ipsum</p>';
        $expected = e($code);
        $snippet = Snippet::factory()->create(['body' => $code]);

        // Act
        $body = $snippet->body;

        // Assert
        $this->assertEquals($expected, e($body));
    }

    /** @test */
    public function it_can_check_is_a_fork_or_not()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->make();
        $snippet->addFork($fork);

        // Act
        $fork_is_fork = $fork->is_fork;
        $snippet_is_fork = $snippet->is_fork;

        // Assert
        $this->assertTrue($fork_is_fork);
        $this->assertFalse($snippet_is_fork);
    }

    /** @test */
    public function it_can_check_is_have_children()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->make();
        $snippet->addFork($fork);

        // Act
        $snippet_have_forks = $snippet->have_forks;
        $fork_have_forks = $fork->have_forks;

        // Assert
        $this->assertTrue($snippet_have_forks);
        $this->assertFalse($fork_have_forks);
    }

    /** @test */
    public function it_can_check_is_a_parent()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->make();
        $snippet->addFork($fork);

        // Act
        $snippet_is_parent = $snippet->is_parent;
        $fork_is_parent = $fork->is_parent;

        // Assert
        $this->assertTrue($snippet_is_parent);
        $this->assertFalse($fork_is_parent);
    }

    /** @test */
    public function it_can_get_creator()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create();
        $user->addSnippet($snippet);

        // Act
        $username = $snippet->user->name;

        // Assert
        $this->assertEquals($user->name, $username);
    }

    /** @test */
    public function it_can_show_all_fans()
    {
        // Arrange
        $fans_amount = 2;
        $total_users_amount = 4;
        $users = User::factory()->count($total_users_amount)->make();
        $users->each(function ($user) {
            $user->save();
            $user->generateToken();
        });
        $snippet = Snippet::factory()->create();
        $users->take($fans_amount)->each(function($user) use ($snippet) {
            $user->addToFavoriteSnippets($snippet);
        });

        // Act
        $snippet_fans = $snippet->fans;

        // Assert
        $this->assertCount($fans_amount, $snippet_fans);
    }

    /** @test */
    public function it_can_show_quantity_of_fans()
    {
        // Arrange
        $total_users_amount = 10;
        $fans_amount = 7;
        $users = User::factory()->count($total_users_amount)->make();
        $users->each(function($user) {
            $user->save();
            $user->generateToken();
        });
        $snippet = Snippet::factory()->create();
        $users->take($fans_amount)->each(function($user) use ($snippet) {
            $user->addToFavoriteSnippets($snippet);
        });

        // Act
        $quantity = $snippet->fans_quantity;

        // Assert
        $this->assertEquals($fans_amount, $quantity);
    }

    /** @test */
    public function it_can_get_forks_quantity()
    {
        // Arrange
        $expected_forks_amount = 7;
        $parent_snippet = Snippet::factory()->create();
        foreach(range(1, $expected_forks_amount) as $step) {
            Snippet::factory()->create(['fork_id' => $parent_snippet]);
        }

        // Act
        $quantity = $parent_snippet->forks_quantity;

        // Assert
        $this->assertEquals($expected_forks_amount, $quantity);
    }

    /** @test */
    public function it_can_add_fork()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->make();

        // Act
        $snippet->addFork($fork);

        // Assert
        $this->assertEquals(1, $snippet->forks_quantity);
    }

    /** @test */
    public function it_can_remove_fork()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $fork = Snippet::factory()->make();
        $snippet->addFork($fork);
        $this->assertEquals(1, $snippet->forks_quantity);

        // Act
        $snippet->removeFork($fork);

        // Assert
        $this->assertEquals(0, $snippet->forks_quantity);
    }

    /** @test */
    public function it_can_become_parent_when_his_parent_is_deleted()
    {
        // Arrange
        $forks_amount = 3;
        $expected_forks_amount_after_deletion = $forks_amount - 1;
        $grandpa_snippet = Snippet::factory()->create();
        $father_snippet = Snippet::factory()->make();
        $child_snippet = Snippet::factory()->make();
        $grandpa_snippet->addFork($father_snippet);
        $father_snippet->addFork($child_snippet);
        $this->assertCount($forks_amount, Snippet::all());

        // Act
        $grandpa_snippet->removeFork($father_snippet);

        // Assert
        $this->assertTrue($child_snippet->fresh()->is_parent);
        $this->assertCount($expected_forks_amount_after_deletion, Snippet::all());
    }

    /** @test */
    public function snippet_can_have_many_tags()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $tag1 = Tag::factory()->make(['name' => 'foo']);
        $tag2 = Tag::factory()->make(['name' => 'bar']);
        $tag3 = Tag::factory()->make(['name' => 'baz']);
        $snippet->addTag($tag1);
        $snippet->addTag($tag2);
        $snippet->addTag($tag3);

        // Act
        $tags_from_snippet = $snippet->tags;

        // Assert
        $this->assertEquals($tag1->name, $tags_from_snippet[0]->name);
        $this->assertEquals($tag2->name, $tags_from_snippet[1]->name);
        $this->assertEquals($tag3->name, $tags_from_snippet[2]->name);
    }

    /** @test */
    public function it_can_get_forks_with_nested_forks()
    {
        // Arrange
        [$snippet1, $snippet2, $snippet3, $snippet4, $snippet5, $snippet6, $snippet7, $snippet8, $snippet9, $snippet10] = Snippet::factory()->count(10)->create();
        $snippet1
            ->addFork($snippet2)
            ->forks()->first()->addFork($snippet3)
            ->forks()->first()->addFork($snippet4)
            ->forks()->first()->addFork($snippet5)
            ->forks()->first()->addFork($snippet6)
            ->forks()->first()->addFork($snippet7)
            ->forks()->first()->addFork($snippet8)
            ->forks()->first()->addFork($snippet9)
            ->forks()->first()->addFork($snippet10);

        // Act
        $forks = $snippet1->forks->toArray();

        // Assert
        $fork = $forks[0];
        $this->assertEquals(2, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(3, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(4, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(5, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(6, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(7, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(8, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(9, $fork['id']);
        $fork = $fork['forks'][0];
        $this->assertEquals(10, $fork['id']);
    }

    /** @test */
    public function it_can_register_and_store_copy_actions()
    {
        // Arrange
        $snippet = Snippet::factory()->create();

        // Act
        $snippet
            ->copy()
            ->copy()
            ->copy();

        // Assert
        $this->assertEquals(3, $snippet->fresh()->actions->times_copied);
    }

    /** @test */
    public function it_can_access_times_copied_directly()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $snippet
            ->copy()
            ->copy()
            ->copy();

        // Act
        $times_copied = $snippet->times_copied;

        // Assert
        $this->assertEquals(3, $times_copied);
    }

    /** @test */
    public function it_can_get_his_settings()
    {
        // Arrange
        $snippet = Snippet::factory()->create();
        $snippet->settings = '{"theme":"darcula"}';
        $snippet->save();

        // Act
        $settings = $snippet->fresh()->settings;

        // Assert
        $this->assertEquals('{"theme":"darcula"}', $settings);
    }

}
