<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnippetsCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_snippets_form()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('snippets.create'));

        $response->assertStatus(200);
        $response->assertSee('New snippet');
        $response->assertSee('title');
        $response->assertSee('Body');
        $response->assertSee('Publish Snippet');
    }

    /** @test */
    public function user_can_create_snippet()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('snippets.store'), [
            'title' => 'some title',
            'body' => 'some body',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('snippets.index'));
        $this->assertDatabaseHas('snippets', [
            'title' => $user->snippets->first()->title,
            'body' => $user->snippets->first()->body,
        ]);
    }

    /** @test */
    public function guest_cannot_see_snippet_form()
    {
        $response = $this->get(route('snippets.create'));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_store_snippets()
    {
        $response = $this->post(route('snippets.store'), [
            'title' => 'some title',
            'body' => 'some body',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
