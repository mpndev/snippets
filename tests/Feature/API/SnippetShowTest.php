<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetShowTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.show';

    protected $test_verb = 'get';

    /** @test */
    public function guest_can_see_single_snippet_with_all_useful_fields()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonFragment($snippet->fresh()->toArray());
    }
}
