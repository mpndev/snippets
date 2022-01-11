<?php

namespace Tests\Feature\API;

use App\User;
use App\Snippet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.destroy';

    protected $test_verb = 'delete';

    /** @test */
    public function admin_can_delete_user()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);
        $user->addRole('admin');
        $user->getRole('admin')->addAbilityTo('manage_users');
        $some_user = User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('snippets', ['id' => $some_user->id]);
    }

    /** @test */
    public function guest_can_not_delete_specific_user()
    {
        // Arrange
        $some_user = User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
        ]);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.'
                ]
            ]);
        $this->assertDatabaseHas('users', ['id' => $some_user->id]);
    }

    /** @test */
    public function user_can_not_delete_another_user()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);
        $some_user = User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.'
                ]
            ]);
        $this->assertDatabaseHas('users', ['id' => $some_user->id]);
    }

    /** @test */
    public function user_can_delete_his_user_profile()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }



    /** @test */
    public function user_delete_all_snippets_when_profile_id_deleted()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);
        $some_user = User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);

        Snippet::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        Snippet::factory()->count(7)->create([
            'user_id' => $some_user->id,
        ]);

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertSame(7, count(Snippet::all()));
    }

}
