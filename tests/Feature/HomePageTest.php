<?php

namespace Tests\Feature;

use App\Snippet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function see_minimum_text()
    {
        factory(User::class)->create();
        $snippet = factory(Snippet::class)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($snippet->title);
        $response->assertSee(htmlspecialchars($snippet->body));
    }

    /** @test */
    public function user_can_see_create_snippet_button()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('Create Snippet');
        $response->assertDontSee('To create snippet you must be');
    }

    /** @test */
    public function guest_do_not_see_create_snippet_button()
    {
        factory(User::class)->create();

        $response = $this->get('/');

        $response->assertDontSee('Create Snippet');
        $response->assertSee('To create snippet you must be');
    }

    /** @test */
    public function pagination_is_available()
    {
        factory(User::class)->create();
        $snippets = factory(Snippet::class, 100)->create();

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('snippets 5/' . $snippets->count())
            ->assertSee('next page')
            ->assertSee('last page')
            ->assertSee('>1</a>')
            ->assertSee('>2</a>')
            ->assertSee('>3</a>');
    }

    /** @test */
    public function see_user_name_when_he_is_logged_in()
    {
        $user = factory(User::class)->create(['name' => 'John Doe']);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('John Doe');
    }

    /** @test */
    public function guest_cannot_see_Fork_me_buttons()
    {
        factory(Snippet::class)->create();

        $response = $this->get('/');
        $response->assertSee("disabled");
        $response->assertSee("Fork Me");
    }

    /** @test */
    public function guest_cannot_access_form_for_forking()
    {
        $snippet = factory(Snippet::class)->create();

        $response = $this->post(route('snippets.forks.store', ['snippet' => $snippet->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
