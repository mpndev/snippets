<?php

namespace Tests\Feature\API;

use App\Snippet;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.update';

    protected $test_verb = 'put';

    /** @test */
    public function user_can_update_snippet()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make([
            'title' => 'foo',
            'description' => 'foo',
            'body' => 'foo'
        ]);
        $user->addSnippet($snippet);
        $new_data = [
            'title' => 'bar',
            'description' => 'bar',
            'body' => 'bar',
            'api_token' => $user->fresh()->api_token,
        ];

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson($snippet->fresh()->toArray());
        $this->assertDatabaseHas('snippets', ['id' => $snippet->id]);
    }

    /** @test */
    public function user_cannot_update_other_users_snippets()
    {
        // Arrange
        $user1 = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $user2 = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet = factory(Snippet::class)->make([
            'title' => 'foo',
            'description' => 'foo',
            'body' => 'foo'
        ]);
        $user1->addSnippet($snippet);
        $new_data = [
            'title' => 'bar',
            'description' => 'bar',
            'body' => 'bar',
            'api_token' => $user2->api_token,
        ];

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.',
                ],
            ]);
    }

    /** @test */
    public function guest_cannot_update_snippet()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make([
            'title' => 'foo',
            'description' => 'foo',
            'body' => 'foo'
        ]);
        $user->addSnippet($snippet);
        $new_data = [
            'title' => 'bar',
            'description' => 'bar',
            'body' => 'bar',
        ];

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id
        ], $new_data);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.',
                ],
            ]);
        $this->assertDatabaseHas('snippets', ['id' => $snippet->id]);
        $this->assertDatabaseMissing('snippets', $new_data);
    }

    /** @test */
    public function validation_rules_on_update_snippet_are_active()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
        $user->addSnippet($snippet);
        $new_data = [
            'title' => str_repeat('A', 255) . 'A',
            'description' => str_repeat('A', 2000) . 'A',
            'body' => str_repeat('A', 100000) . 'A',
            'api_token' => $user->api_token,
        ];

        // Act
        $response = $this->apiRequest([
            'snippet' => $snippet->id,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'title' => [
                    'The title may not be greater than 255 characters.',
                ],
                'description' => [
                    'The description may not be greater than 2000 characters.',
                ],
                'body' => [
                    'The body may not be greater than 100000 characters.',
                ],
            ]);
    }

    /** @test */
    public function empty_description_will_be_converted_to_empty_string()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make([
            'title' => 'foo',
            'description' => 'foo',
            'body' => 'foo'
        ]);
        $user->addSnippet($snippet);
        $new_data = [
            'title' => 'bar',
            'description' => null,
            'body' => 'bar',
            'api_token' => $user->fresh()->api_token,
        ];

        // Act
        $this->apiRequest([
            'snippet' => $snippet->id,
        ], $new_data);

        // Assert
        $snippet = $user->snippets()->first();
        $this->assertSame('', $snippet->description);
    }

}
