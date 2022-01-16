<?php

namespace Tests\Feature\API;

use App\Models\User;
use App\Models\Snippet;
use Tests\TestCase;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.store';

    protected $test_verb = 'post';

    /** @test */
    public function user_can_submit_new_snippet()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([], [
            'title' => 'Foo',
            'description' => 'Bar',
            'body' => '<h1>FooBar</h1>',
            'settings' => '{"theme": "darcula"}',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $snippet = $user->snippets()->first();
        $response
            ->assertStatus(201)
            ->assertJson($snippet->toArray());
        $this->assertDatabaseHas('snippets', ['id' => $snippet->id]);
    }

    /** @test */
    public function user_can_not_submit_new_snippet_with_existing_title()
    {
        // Arrange
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);

        $another_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $another_user->addSnippet(Snippet::factory()->make([
            'title' => 'Foo',
            'slug' => 'foo',
            'description' => 'Bar',
            'body' => '<h1>FooBar</h1>',
            'settings' => '{"theme": "darcula"}',
        ]));

        // Act
        $response = $this->apiRequest([], [
            'title' => 'Foo',
            'description' => 'Bar',
            'body' => '<h1>FooBar</h1>',
            'settings' => '{"theme": "darcula"}',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'title' => [
                    'The title has already been taken.',
                ],
            ]);
        $this->assertDatabaseMissing('snippets', ['id' => 2]);
    }

    /** @test */
    public function guest_cannot_store_new_snippet()
    {
        // Arrange

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.'
                ],
            ]);
    }

    /** @test */
    public function title_and_body_fields_are_required()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([], [
            'title' => null,
            'description' => null,
            'body' => null,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'title' => [
                    'The title field is required.',
                ],
                'body' => [
                    'The body field is required.',
                ],
            ]);
    }

    /** @test */
    public function title_description_and_body_fields_have_limitations()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([], [
            'title' => str_repeat('A', 255) . 'A',
            'description' => str_repeat('A', 2000) . 'A',
            'body' => str_repeat('A', 100000) . 'A',
            'api_token' => $user->api_token,
        ]);

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
    public function when_user_store_snippet_if_is_a_fork_title_cannot_be_like_parent_title()
    {
        // Arrange
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
        $user->addSnippet($snippet);

        // Act
        $response = $this->apiRequest([], [
            '_parent_id' => $snippet->id,
            'title' => $snippet->title,
            'description' => $snippet->description,
            'body' => $snippet->body,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'title' => [
                    'The title has already been taken.',
                ],
            ]);
    }

    /** @test */
    public function empty_description_will_be_converted_to_empty_string()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $this->apiRequest([], [
            'title' => 'Foo',
            'description' => null,
            'body' => '<h1>FooBar</h1>',
            'settings' => '{"theme": "darcula"}',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $snippet = $user->snippets()->first();
        $this->assertSame('', $snippet->description);
    }


    /** @test */
    public function when_user_submit_new_snippet_slug_is_created()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([], [
            'title' => 'Foo Bar',
            'description' => 'Bar',
            'body' => '<h1>FooBar</h1>',
            'settings' => '{"theme": "darcula"}',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $snippet = $user->snippets()->first();
        $response
            ->assertStatus(201)
            ->assertJson($snippet->toArray());
        $this->assertDatabaseHas('snippets', ['slug' => 'foo-bar']);
    }

}
