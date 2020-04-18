<?php

namespace Tests\Feature\API;

use App\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagsIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.tags.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_can_get_all_tags()
    {
        // Arrange
        $tags = factory(Tag::class, 3)->create();

        // Act
        $response = $this->apiRequest();

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tags[0]->name])
            ->assertJsonFragment(['name' => $tags[1]->name])
            ->assertJsonFragment(['name' => $tags[2]->name]);
    }

}
