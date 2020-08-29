<?php

namespace Tests\Feature\API;

use App\Snippet;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnippetsCanBeCached extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function user_must_see_cached_snippets_after_first_request()
    {
        // Arrange
        $cache_key = 'user_id->null|my_snippets->null|snippets_by_author->null|snippets_created_at_the_same_day_as->null|my_forked_snippets->null|forks_of_my_snippets->null|my_favorite_snippets->null|with_tags->null|search->null|latest->null|most_liked_snippets->null|most_copied_snippets->null|limit->null|page->null';
        factory(Snippet::class)->create(['description' => 'foo']);
        $this->assertNull(Cache::get($cache_key));

        // Act
        $this->apiRequest();

        // Assert
        $this->assertNotNull(Cache::get($cache_key));
        $this->assertArrayContainsRecursive('foo', Cache::get($cache_key));

    }

}
