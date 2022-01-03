<?php

namespace Tests\Feature\API;

use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActionsCopySnippetTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.actions.copy.update';

    protected $test_verb = 'put';

    /** @test */
    public function snippet_can_be_copied()
    {
        // Arrange
        $snippet = Snippet::factory()->create();

        // Act
        $response = $this->apiRequest([
            'snippet_id_or_slug' => $snippet->id,
        ]);

        // Assert
        $response
            ->assertStatus(202)
            ->assertJson($snippet->toArray());
        $this->assertEquals(1, $snippet->times_copied);
    }

}
