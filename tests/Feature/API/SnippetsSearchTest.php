<?php

namespace Tests\Feature\API;

use App\Snippet;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APISearchSnippetsTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.snippets.index';

    protected $test_verb = 'get';

    /** @test */
    public function show_all_snippets_that_contains_the_string_in_title_description_or_body()
    {
        // Arrange
        $snippets_data = $this->getSnippetsData();
        factory(Snippet::class, 10)->create([
            'title' => 'qqqqq',
            'description' => 'qqqqqqqqqq',
            'body' => 'qqqq',
        ]);
        foreach($snippets_data as $snippet_data) {
            factory(Snippet::class)->create($snippet_data);
        }

        // Act
        $response = $this->apiRequest([
            'search' => 'N'
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertSeeInOrder(
                [
                    'Nttttttt2',
                    'tttttNtt1',
                    'tttttttt5',
                    'tttttttt4',
                    'tttttttt3',
                ]
            );
    }

    protected function getSnippetsData()
    {
        return [
            [
                'title' => 'tttttNtt1',
                'description' => 'dddddddd',
                'body' => 'bbbbbbbb',
                'created_at' => Carbon::yesterday(),
            ],
            [
                'title' => 'Nttttttt2',
                'description' => 'dddddddd',
                'body' => 'bbbbbbbb',
                'created_at' => Carbon::yesterday(),
            ],
            [
                'title' => 'tttttttt3',
                'description' => 'dddddddd',
                'body' => 'bbbbNbbb',
                'created_at' => Carbon::yesterday(),
            ],
            [
                'title' => 'tttttttt4',
                'description' => 'ddNdddddd',
                'body' => 'bbbbbbbb',
                'created_at' => Carbon::yesterday(),
            ],
            [
                'title' => 'tttttttt5',
                'description' => 'Ndddddddd',
                'body' => 'bbbbbbbb',
                'created_at' => Carbon::yesterday(),
            ],
        ];
    }

}
