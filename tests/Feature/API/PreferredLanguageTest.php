<?php

namespace Tests\Feature\API;

use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PreferredLanguageTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function can_see_bulgarian_translation_on_created_at_for_humans_field()
    {
        // Arrange
        factory(Snippet::class)->create();

        // Act
        $response = $this->withHeader('Langcode', 'bg')->apiRequest();

        // Assert
        $response->assertJsonFragment(['преди 1 секунда']);
    }

}
