<?php

namespace Tests\Feature;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSnippetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_hes_own_snippets()
    {$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $snippets = [
            factory(Snippet::class)->make(['title' => 'snippet 1']),
            factory(Snippet::class)->make(['title' => 'snippet 2']),
            factory(Snippet::class)->make(['title' => 'snippet 3']),
        ];
        $user->snippets()->saveMany($snippets);

        $response = $this->actingAs($user)->get(route('user.snippets', ['user' => $user->id]));

        $response->assertStatus(200);
        $response->assertSee($snippets[0]->title);
        $response->assertSee($snippets[1]->title);
        $response->assertSee($snippets[2]->title);
    }
}
