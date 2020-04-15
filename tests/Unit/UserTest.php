<?php

namespace Tests\Unit;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_snippets()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());

        // Act
        $user_snippets = $user->snippets;

        // Assert
        $this->assertEquals($snippet->id, $user_snippets[0]->id);
        $this->assertEquals($snippet->title, $user_snippets[0]->title);
        $this->assertEquals($snippet->body, $user_snippets[0]->body);
    }

    /** @test */
    public function it_can_check_is_snippet_favorite()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());
        $this->assertFalse($user->isSnippetFavorite($snippet));

        // Act
        $user->addToFavoriteSnippets($snippet);

        // Assert
        $this->assertTrue($user->isSnippetFavorite($snippet));
    }

    /** @test */
    public function it_can_add_snippet_to_favorite_snippets()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = $user->snippets()->save(factory(Snippet::class)->make());

        // Act
        $user->addToFavoriteSnippets($snippet);

        // Assert
        $this->assertCount(1, $user->favoriteSnippets()->get());
    }

    /** @test */
    public function it_can_create_snippet()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();

        // Act
        $user->addSnippet($snippet);

        // Assert
        $this->assertDatabaseHas('snippets', ['id' => $user->snippets()->first()->id]);
    }

    /** @test */
    public function it_can_remove_snippet_from_favorite_snippets()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippets = $user->snippets()->saveMany(factory(Snippet::class, 2)->make());

        // Act
        $user->removeFromFavoriteSnippets($snippets[0]);

        // Assert
        $this->assertCount(0, $user->favoriteSnippets()->get());
    }

    /** @test */
    public function it_can_get_hes_snippets_quantity()
    {
        // Arrange
        $user = factory(User::class)->create();
        $user->snippets()->saveMany(factory(Snippet::class, 10)->make());

        // Act
        $quantity = $user->snippets_quantity;

        // Assert
        $this->assertEquals(10, $quantity);
    }

    /** @test */
    public function it_can_get_hes_favorite_snippets_quantity()
    {
        // Arrange
        $snippets_amount = 10;
        $expected_favorite_snippets_amount = 5;
        $user = factory(User::class)->create();
        $snippets = $user->snippets()->saveMany(factory(Snippet::class, $snippets_amount)->make());
        $snippets->take($expected_favorite_snippets_amount)->each(function($snippet) use ($user){
            $user->addToFavoriteSnippets($snippet);
        });

        // Act
        $quantity = $user->favorite_snippets_quantity;

        // Assert
        $this->assertEquals($expected_favorite_snippets_amount, $quantity);
    }

    /** @test */
    public function it_remove_snippet_from_favorite_snippets_when_snippet_is_deleted()
    {
        // Arrange
        $user = factory(User::class)->create(['api_token' => str_repeat('A', 60)]);
        $some_user = factory(User::class)->create(['api_token' => str_repeat('B', 60)]);
        $snippet = factory(Snippet::class)->make();
        $some_user->addSnippet($snippet);
        $user->addToFavoriteSnippets($snippet);

        // Act
        $some_user->removeSnippet($snippet);

        // Assert
        $this->assertSame(0, $user->favorite_snippets_quantity);
    }

    /** @test */
    public function it_delete_his_snippet()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->make();
        $user->addSnippet($snippet);
        $this->assertEquals(1, $user->snippets_quantity);

        // Act
        $user->removeSnippet($snippet);

        // Assert
        $this->assertEquals(0, $user->snippets_quantity);
    }

    /** @test */
    public function it_can_register_and_store_copy_actions_on_snippet()
    {
        // Arrange
        $user = factory(User::class)->create();
        $snippet = factory(Snippet::class)->create();
        $snippet->actions()->create();

        // Act
        $user->copy($snippet->fresh())
            ->copy($snippet->fresh())
            ->copy($snippet->fresh());

        // Assert
        $this->assertEquals(3, $snippet->actions->times_copied);
    }

    /** @test */
    public function it_can_get_his_settings()
    {
        // Arrange
        $user = factory(User::class)->create();

        // Act
        $settings = $user->settings;

        // Assert
        $this->assertEquals('{"theme":"default"}', $settings);
    }
}
