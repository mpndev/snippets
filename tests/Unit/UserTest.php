<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Models\Snippet;
use App\Models\Ability;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_snippets()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = $user->snippets()->save(Snippet::factory()->make());

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
        $user = User::factory()->create();
        $snippet = $user->snippets()->save(Snippet::factory()->make());
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
        $user = User::factory()->create();
        $snippet = $user->snippets()->save(Snippet::factory()->make());

        // Act
        $user->addToFavoriteSnippets($snippet);

        // Assert
        $this->assertCount(1, $user->favoriteSnippets()->get());
    }

    /** @test */
    public function it_can_create_snippet()
    {
        // Arrange
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();

        // Act
        $user->addSnippet($snippet);

        // Assert
        $this->assertDatabaseHas('snippets', ['id' => $user->snippets()->first()->id]);
    }

    /** @test */
    public function it_can_remove_snippet_from_favorite_snippets()
    {
        // Arrange
        $user = User::factory()->create();
        $snippets = $user->snippets()->saveMany(Snippet::factory()->count(2)->make());

        // Act
        $user->removeFromFavoriteSnippets($snippets[0]);

        // Assert
        $this->assertCount(0, $user->favoriteSnippets()->get());
    }

    /** @test */
    public function it_can_get_hes_snippets_quantity()
    {
        // Arrange
        $user = User::factory()->create();
        $user->snippets()->saveMany(Snippet::factory()->count(10)->make());

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
        $user = User::factory()->create();
        $snippets = $user->snippets()->saveMany(Snippet::factory()->count($snippets_amount)->make());
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
        $user = User::factory()->create(['api_token' => str_repeat('A', 60)]);
        $some_user = User::factory()->create(['api_token' => str_repeat('B', 60)]);
        $snippet = Snippet::factory()->make();
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
        $user = User::factory()->create();
        $snippet = Snippet::factory()->make();
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
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create();
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
        $user = User::factory()->create();

        // Act
        $settings = $user->settings;

        // Assert
        $this->assertEquals('{"theme":"default"}', $settings);
    }

    /** @test */
    public function it_can_add_role_dummy()
    {
        // Arrange
        $user = User::factory()->create();
        $dummy_role = Role::factory()->create([
            'name' => 'dummy',
            'label' => 'Dummy role for testing',
        ]);

        // Act
        $user->addRole($dummy_role);

        // Assert
        $roles = $user->getRoles();
        $this->assertArrayContainsRecursive('dummy', $roles);
        $this->assertArrayContainsRecursive('Dummy role for testing', $roles);
    }

    /** @test */
    public function it_can_add_role_dummy_role_as_string()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $user->addRole('dummy_role');

        // Assert
        $roles = $user->getRoles();
        $this->assertArrayContainsRecursive('dummy_role', $roles);
        $this->assertArrayContainsRecursive('Dummy Role', $roles);
    }

    /** @test */
    public function it_can_not_add_role_dummy_role_as_string_if_no_underscore()
    {
        try {
            // Arrange
            $user = User::factory()->create();

            // Act
            $user->addRole('dummy role');

        } catch (\Exception $e) {
            // Assert
            $this->assertSame("Role name cannot use spaces. Use underscores instead!", $e->getMessage());
        }
    }

    /** @test */
    public function it_can_add_ability_to_manage_users()
    {
        // Arrange
        $user = User::factory()->create();
        $admin_role = Role::factory()->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);
        $manage_users = Ability::factory()->create([
            'name' => 'manage_users',
            'label' => 'Manage users',
        ]);

        // Act
        $admin_role->addAbilityTo($manage_users);
        $user->addRole($admin_role);

        // Assert
        $abilities = $user->abilities();
        $this->assertSame('manage_users', $abilities[0]);
    }

    /** @test */
    public function it_can_get_role_by_name()
    {
        // Arrange
        $user = User::factory()->create();
        $admin_role = Role::factory()->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);
        $user->addRole($admin_role);

        // Act
        $role = $user->getRole('admin');

        // Assert
        $this->assertSame('admin', $role->name);
    }

    /** @test */
    public function it_can_get_role_by_name_and_attach_ability_to_it()
    {
        // Arrange
        $user = User::factory()->create();
        $admin_role = Role::factory()->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);
        $user->addRole($admin_role);
        $user->getRole('admin')->addAbilityTo('manage_users');

        // Act
        $ability = $user->getAbility('manage_users');

        // Assert
        $this->assertSame('manage_users', $ability->name);
    }

    /** @test */
    public function it_gets_no_ability_if_no_role_was_added()
    {
        // Arrange
        $user = User::factory()->create();
        $admin_role = Role::factory()->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);
        $manage_users = Ability::factory()->create([
            'name' => 'manage_users',
            'label' => 'Manage users',
        ]);

        // Act
        $admin_role->addAbilityTo($manage_users);

        // Assert
        $abilities = $user->abilities();
        $this->assertSame([], $abilities);
        $this->assertSameSize([], $abilities);
    }

    /** @test */
    public function it_can_check_if_has_role()
    {
        // Arrange
        $user = User::factory()->create();
        $admin_role = Role::factory()->create([
            'name' => 'dummy',
            'label' => 'Dummy role for testing',
        ]);
        $user->addRole($admin_role);

        // Act
        $have_dummy_role = $user->hasRole('dummy');
        $do_not_have_dummy_role = $user->hasRole('smarty');
        $get_false_for_no_role = $user->hasRole();

        // Assert
        $this->assertTrue($have_dummy_role);
        $this->assertFalse($do_not_have_dummy_role);
        $this->assertFalse($get_false_for_no_role);
    }

    /** @test */
    public function it_can_check_if_has_ability()
    {
        // Arrange
        $user = User::factory()->create();
        $dummy_role = Role::factory()->create([
            'name' => 'dummy',
            'label' => 'Dummy role for testing',
        ]);
        $manage_users = Ability::factory()->create([
            'name' => 'manage_users',
            'label' => 'Manage users',
        ]);
        $dummy_role->addAbilityTo($manage_users);
        $user->addRole($dummy_role);

        // Act
        $have_manage_users_ability = $user->hasAbility('manage_users');
        $do_not_have_delete_users_ability = $user->hasAbility('smarty');
        $get_false_for_no_ability = $user->hasAbility();

        // Assert
        $this->assertTrue($have_manage_users_ability);
        $this->assertFalse($do_not_have_delete_users_ability);
        $this->assertFalse($get_false_for_no_ability);
    }
}
