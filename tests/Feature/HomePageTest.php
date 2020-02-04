<?php

namespace Tests\Feature;

use App\Snippet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{git
    use RefreshDatabase;

    /** @test */
    public function see_minimum_text()
    {
        $snippet = factory(Snippet::class)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Create Snippet');
        $response->assertSee('Fork Me');
        $response->assertSee($snippet->title);
        $response->assertSee(htmlspecialchars($snippet->body));
    }

    /** @test */
    public function pagination_is_available()
    {
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

}
