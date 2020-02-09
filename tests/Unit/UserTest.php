<?php

namespace Tests\Unit;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_snippets()
    {
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());

        $this->assertEquals($user->snippets[0]->id, $snippet->id);
        $this->assertEquals($user->snippets[0]->title, $snippet->title);
        $this->assertEquals($user->snippets[0]->body, $snippet->body);
    }

    /** @test */
    public function user_can_get_snippets_that_was_forked()
    {
        $user = factory(User::class)->create();
        $snippet1 = $user->snippets()->save(factory(Snippet::class)->make());
        $snippet2 = $user->snippets()->save(factory(Snippet::class)->make());
        $user->snippets()->saveMany(factory(Snippet::class, 5)->make(['fork_id' => $snippet1->id]));
        $user->snippets()->saveMany(factory(Snippet::class, 5)->make(['fork_id' => $snippet2->id]));

        $this->assertEquals(2, $user->paginatedForkedSnippets()->count());
        $this->assertEquals(12, $user->snippets->count());
    }
}
