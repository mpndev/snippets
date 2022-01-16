<?php

namespace Tests\Feature\API;

use App\Models\User;
use App\Models\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MostCopiedSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function guest_can_see_top_5_most_copied_snippets()
    {
        // Arrange
        $snippets = Snippet::factory()->count(10)->create(['user_id' => 99]);
        $this->repeat(function($i) use($snippets) {
            $snippets[$i]->copy();
            $snippets[$i] = $snippets[$i]->fresh();
        });

        // Act
        $response = $this->apiRequest([
            'most-copied-snippets',
            'limit' => 5
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['id' => $snippets[2]->id])
            ->assertJsonFragment(['id' => $snippets[4]->id])
            ->assertJsonFragment(['id' => $snippets[7]->id])
            ->assertJsonFragment(['id' => $snippets[8]->id])
            ->assertJsonFragment(['id' => $snippets[9]->id])
            ->assertJsonMissingExact(['id' => $snippets[0]->id])
            ->assertJsonMissingExact(['id' => $snippets[1]->id])
            ->assertJsonMissingExact(['id' => $snippets[3]->id])
            ->assertJsonMissingExact(['id' => $snippets[5]->id])
            ->assertJsonMissingExact(['id' => $snippets[6]->id]);
        /*
         * Json expect to see:
         * snippet with id 3 copied 8 times
         * snippet with id 5 copied 7 times
         * snippet with id 9 copied 5 times
         * snippet with id 8 copied 4 times
         * snippet with id 10 copied 4 times
         */
    }

    protected function repeat($callback)
    {
        $targets = [2, 2, 2, 2, 2, 2, 2, 2, 4, 4, 4, 4, 4, 4, 4, 7, 7, 7, 7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 1, 1, 6, 6];
        foreach($targets as $target_index) {
            $callback($target_index);
        }
    }

}
