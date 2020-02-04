<?php

namespace Tests\Unit;

use App\Snippet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
}
