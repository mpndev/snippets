<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Snippet;

class ShippetsShowPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function snippet_can_have_forks()
    {
        $parent_snippet = factory(Snippet::class)->create();
        $first_fork = factory(Snippet::class)->create([
            'fork_id' => $parent_snippet->id,
            'title' => 'Title-1',
        ]);
        $second_fork = factory(Snippet::class)->create([
            'fork_id' => $parent_snippet->id,
            'title' => 'Title-2',
        ]);

        $response = $this->get(route('snippets.show', ['snippet' => $parent_snippet->id]));

        $response->assertStatus(200)
            ->assertSee($parent_snippet->title)
            ->assertSee(htmlspecialchars($parent_snippet->body))
            ->assertSee($first_fork->title)
            ->assertSee($second_fork->title);
    }

    /** @test */
    public function snippet_can_have_parent()
    {
        $parent_snippet = factory(Snippet::class)->create([
            'title' => 'Title-1',
        ]);
        $child_fork = factory(Snippet::class)->create(['fork_id' => $parent_snippet->id]);

        $response = $this->get(route('snippets.show', ['snippet' => $child_fork->id]));

        $response->assertStatus(200)
            ->assertSee($child_fork->title)
            ->assertSee(htmlspecialchars($child_fork->body))
            ->assertSee($parent_snippet->title);
    }
}
