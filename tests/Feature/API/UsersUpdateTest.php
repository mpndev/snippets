<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.update';

    protected $test_verb = 'put';

    /** @test */
    public function admin_can_update_user()
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

        $new_data = [
            'name' => 'John Doe',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
            'api_token' => $user->api_token,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson($some_user->fresh()->toArray());
        $this->assertSame('John Doe', $some_user->fresh()->name);
    }

    /** @test */
    public function guest_can_not_update_specific_user()
    {
        // Arrange
        $some_user = User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);

        $new_data = [
            'name' => 'John Doe',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.',
                ],
            ]);
        $this->assertSame('John', $some_user->fresh()->name);
    }

    /** @test */
    public function user_can_not_update_another_user()
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

        $new_data = [
            'name' => 'John Doe',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $some_user->id,
            'api_token' => $user->api_token,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(403)
            ->assertJson([
                'user' => [
                    'This action is unauthorized.',
                ],
            ]);
        $this->assertSame('John', $some_user->fresh()->name);
    }

    /** @test */
    public function user_can_update_his_name()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);

        $new_data = [
            'name' => 'John Doe',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
            'api_token' => $user->api_token,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson($user->fresh()->toArray());
        $this->assertSame('John Doe', $user->fresh()->name);
    }

    /** @test */
    public function user_can_update_his_password()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'password' => 'qwerty123',
            'api_token' => str_repeat('A', 60)
        ]);

        $new_data = [
            'name' => 'John Doe',
            'password' => 'qwerty987',
            'password_confirmation' => 'qwerty987',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
            'api_token' => $user->api_token,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson($user->fresh()->toArray());
        $this->assertSame('John Doe', $user->fresh()->name);
    }

    /** @test */
    public function user_can_not_update_his_password_if_password_confirmation_is_in_miss_mach()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'password' => 'qwerty123',
            'api_token' => str_repeat('A', 60)
        ]);

        $new_data = [
            'name' => 'John Doe',
            'password' => 'qwerty987',
            'password_confirmation' => 'qwerty321',
        ];

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
            'api_token' => $user->api_token,
        ], $new_data);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password confirmation does not match.',
                ]
            ]);
    }

}
