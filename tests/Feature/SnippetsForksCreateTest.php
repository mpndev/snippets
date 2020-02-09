<?php

namespace Tests\Feature;

use App\Snippet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnippetsForksCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_fork_form()
    {
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->create([
            'title' => 'Parent title',
            'body' => 'Parent body',
        ]);

        $response = $this->actingAs($user)->get(route('snippets.forks.create', ['snippet' => $snippet->id]));

        $response->assertStatus(200);
        $response->assertSee('Fork the snippet');
        $response->assertSee($snippet->title);
        $response->assertSee(htmlspecialchars($snippet->body));
        $response->assertSee('Publish Snippet');
    }

    /** @test */
    public function user_can_create_fork()
    {
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->create([
            'title' => 'Parent title',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('snippets.forks.store', ['snippet' => $snippet->id]), [
            'title' => 'Fork title',
            'body' => 'Fork body',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('snippets.index'));
        $this->assertDatabaseHas('snippets', [
            'title' => $user->snippets->first()->title,
            'body' => $user->snippets->first()->body,
        ]);

        $fork = $user->snippets[0];
        $response = $this->actingAs($user)->get(route('snippets.index'));
        $response->assertSee($fork->title);
        $response->assertSee(htmlspecialchars($fork->body));
    }

    /** @test */
    public function guest_cannot_see_forked_snippets_form()
    {
        $snippet = factory(Snippet::class)->create();
        $response = $this->get(route('snippets.forks.create', ['snippet' => $snippet->id]), [
            'title' => 'some title',
            'body' => 'some body',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_store_forked_snippets()
    {
        $snippet = factory(Snippet::class)->create();
        $response = $this->post(route('snippets.forks.store', ['snippet' => $snippet->id]), [
            'title' => 'some title',
            'body' => 'some body',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
