<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MostLikedSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function guest_can_see_top_5_most_liked_snippets()
    {
        // Arrange
        $user1 = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $user2 = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $user3 = User::factory()->create(['api_token' => str_repeat('C', 60)]);
        $user4 = User::factory()->create(['api_token' => str_repeat('D', 60)]);
        $user5 = User::factory()->create(['api_token' => str_repeat('E', 60)]);
        $user6 = User::factory()->create(['api_token' => str_repeat('F', 60)]);
        $user7 = User::factory()->create(['api_token' => str_repeat('G', 60)]);
        $user8 = User::factory()->create(['api_token' => str_repeat('K', 60)]);
        $user9 = User::factory()->create(['api_token' => str_repeat('L', 60)]);
        $user10 = User::factory()->create(['api_token' => str_repeat('M', 60)]);
        for ($i = 1; $i <= 10; $i++) {
            $user1->addSnippet(Snippet::factory()->make(['title' => "dummy title $i"]));
        }
        $snippets_of_first_user = $user1->snippets;

        $user1->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user3->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user4->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user5->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user6->addToFavoriteSnippets($snippets_of_first_user[5]);
        $user7->addToFavoriteSnippets($snippets_of_first_user[5]);

        $user5->addToFavoriteSnippets($snippets_of_first_user[4]);
        $user6->addToFavoriteSnippets($snippets_of_first_user[4]);
        $user7->addToFavoriteSnippets($snippets_of_first_user[4]);
        $user8->addToFavoriteSnippets($snippets_of_first_user[4]);
        $user9->addToFavoriteSnippets($snippets_of_first_user[4]);

        $user1->addToFavoriteSnippets($snippets_of_first_user[3]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[3]);
        $user3->addToFavoriteSnippets($snippets_of_first_user[3]);
        $user4->addToFavoriteSnippets($snippets_of_first_user[3]);
        $user5->addToFavoriteSnippets($snippets_of_first_user[3]);

        $user1->addToFavoriteSnippets($snippets_of_first_user[2]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[2]);
        $user3->addToFavoriteSnippets($snippets_of_first_user[2]);
        $user4->addToFavoriteSnippets($snippets_of_first_user[2]);

        $user1->addToFavoriteSnippets($snippets_of_first_user[1]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[1]);
        $user10->addToFavoriteSnippets($snippets_of_first_user[1]);

        $user1->addToFavoriteSnippets($snippets_of_first_user[0]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[0]);
        $user1->addToFavoriteSnippets($snippets_of_first_user[6]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[6]);
        $user1->addToFavoriteSnippets($snippets_of_first_user[7]);
        $user2->addToFavoriteSnippets($snippets_of_first_user[7]);

        // Act
        $response = $this->apiRequest([
            'most-liked-snippets',
            'limit' => 5
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['title' => $snippets_of_first_user[5]->title])
            ->assertJsonFragment(['title' => $snippets_of_first_user[4]->title])
            ->assertJsonFragment(['title' => $snippets_of_first_user[3]->title])
            ->assertJsonFragment(['title' => $snippets_of_first_user[2]->title])
            ->assertJsonFragment(['title' => $snippets_of_first_user[1]->title])
            ->assertJsonMissingExact(['title' => $snippets_of_first_user[0]->title])
            ->assertJsonMissingExact(['title' => $snippets_of_first_user[6]->title])
            ->assertJsonMissingExact(['title' => $snippets_of_first_user[7]->title])
            ->assertJsonMissingExact(['title' => $snippets_of_first_user[8]->title])
            ->assertJsonMissingExact(['title' => $snippets_of_first_user[9]->title]);
        /*
         * Json expect to see:
         * snippet  1 likes: 2; <- no
         * snippet  2 likes: 3; <- yes
         * snippet  3 likes: 4; <- yes
         * snippet  4 likes: 5; <- yes
         * snippet  5 likes: 5; <- yes
         * snippet  6 likes: 7; <- yes
         * snippet  7 likes: 2; <- no
         * snippet  8 likes: 2; <- no
         * snippet  9 likes: 0; <- no
         * snippet 10 likes: 0; <- no
         */
    }

}
