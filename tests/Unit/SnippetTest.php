<?php

namespace Tests\Unit;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function snippet_can_get_forks()
    {
        $parent_snippet = factory(Snippet::class)->create();
        $first_child = factory(Snippet::class)->create(['fork_id' => $parent_snippet->id]);
        $second_child = factory(Snippet::class)->create(['fork_id' => $parent_snippet->id]);

        $forks = $parent_snippet->forks;

        $this->assertEquals(2, $forks->count());
        $this->assertEquals($forks[0]->id, $first_child->id);
        $this->assertEquals($forks[1]->id, $second_child->id);
    }

    /** @test */
    public function snippet_can_get_his_parent()
    {
        $snippet = factory(Snippet::class)->create();
        $fork = factory(Snippet::class)->create(['fork_id' => $snippet->id]);

        $parent = $fork->parent;

        $this->assertEquals($snippet->id, $parent->id);
    }

    /** @test */
    public function snippet_can_check_is_a_fork_or_not()
    {
        $snippet = factory(Snippet::class)->create();
        $fork = factory(Snippet::class)->create(['fork_id' => $snippet->id]);

        $this->assertTrue($fork->isAFork());
        $this->assertFalse($snippet->isAFork());
    }

    /** @test */
    public function snippet_can_check_is_a_parent_or_not()
    {
        $snippet = factory(Snippet::class)->create();
        $fork = factory(Snippet::class)->create(['fork_id' => $snippet->id]);

        $this->assertTrue($snippet->haveForks());
        $this->assertFalse($fork->haveForks());
    }

    /** @test */
    public function snippet_can_get_creator()
    {
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->create());

        $this->assertEquals($snippet->user->name, $user->name);
    }

}
