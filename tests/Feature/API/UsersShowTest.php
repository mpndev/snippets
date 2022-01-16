<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersShowTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.show';

    protected $test_verb = 'get';

    /** @test */
    public function admin_can_see_user()
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
        $response
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'John']);
    }

    /** @test */
    public function guest_can_not_see_specific_user()
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
                    'Unauthenticated.',
                ],
            ]);
    }

    /** @test */
    public function user_can_not_see_another_user()
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
                ],
            ]);
    }

    /** @test */
    public function user_can_see_his_user_profile()
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
        $response
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $user->name]);
    }

}
