<?php

namespace Tests\Feature;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserForkedSnippetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_hes_own_snippets_that_was_forked()
    {
        $this->withoutExceptionHandling();
        $user1 = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
        $user1->snippets()->save($snippet);
        $user2 = factory(User::class)->create();
        $fork = factory(Snippet::class)->make(['fork_id' => $snippet->id]);
        $user2->snippets()->save($fork);

        $response = $this->actingAs($user1)->get(route('user.forked-snippets', ['user' => $user1->name]));

        $response->assertStatus(200);
        $response->assertSee('My snippets that was forked');
        $response->assertSee('My forked snippets (' . $user1->paginatedForkedSnippets()->count() . ')');
        $response->assertSee($snippet->title);
        $response->assertSee($fork->title);
        $response->assertSee(htmlspecialchars($fork->body));
    }
}
