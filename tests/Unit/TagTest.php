<?php

namespace Tests\Unit;

use App\Tag;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_have_many_snippets()
    {
        // Arrange
        $snippet1 = factory(Snippet::class)->make(['title' => 'foo']);
        $snippet2 = factory(Snippet::class)->make(['title' => 'bar']);
        $snippet3 = factory(Snippet::class)->make(['title' => 'baz']);
        $tag = factory(Tag::class)->create();
        $tag->addSnippet($snippet1);
        $tag->addSnippet($snippet2);
        $tag->addSnippet($snippet3);

        // Act
        $snippets_from_tag = $tag->snippets;

        // Assert
        $this->assertEquals($snippet1->title, $snippets_from_tag[0]->title);
        $this->assertEquals($snippet2->title, $snippets_from_tag[1]->title);
        $this->assertEquals($snippet3->title, $snippets_from_tag[2]->title);
    }

}
