<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.index';

    protected $test_verb = 'get';

    /** @test */
    public function admin_can_see_all_users()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);
        $user->addRole('admin');
        $user->getRole('admin')->addAbilityTo('manage_users');
        User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);
        User::factory()->create([
            'name' => 'Jane',
            'api_token' => str_repeat('C', 60)
        ]);
        User::factory()->create([
            'name' => 'Johny',
            'api_token' => str_repeat('D', 60)
        ]);
        User::factory()->create([
            'name' => 'Mery',
            'api_token' => str_repeat('E', 60)
        ]);
        User::factory()->create([
            'name' => 'Stiven',
            'api_token' => str_repeat('F', 60)
        ]);

        // Act
        $response = $this->apiRequest([
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(206)
            ->assertJsonFragment(['name' => 'mpndev'])
            ->assertJsonFragment(['name' => 'John'])
            ->assertJsonFragment(['name' => 'Jane'])
            ->assertJsonFragment(['name' => 'Johny'])
            ->assertJsonFragment(['name' => 'Mery'])
            ->assertJsonFragment(['name' => 'Stiven']);
    }

    /** @test */
    public function guest_can_not_see_all_users()
    {
        // Arrange
        User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);
        User::factory()->create([
            'name' => 'Jane',
            'api_token' => str_repeat('C', 60)
        ]);
        User::factory()->create([
            'name' => 'Johny',
            'api_token' => str_repeat('D', 60)
        ]);
        User::factory()->create([
            'name' => 'Mery',
            'api_token' => str_repeat('E', 60)
        ]);
        User::factory()->create([
            'name' => 'Stiven',
            'api_token' => str_repeat('F', 60)
        ]);

        // Act
        $response = $this->apiRequest();

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
    public function user_can_not_see_all_users()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'mpndev',
            'api_token' => str_repeat('A', 60)
        ]);
        User::factory()->create([
            'name' => 'John',
            'api_token' => str_repeat('B', 60)
        ]);
        User::factory()->create([
            'name' => 'Jane',
            'api_token' => str_repeat('C', 60)
        ]);
        User::factory()->create([
            'name' => 'Johny',
            'api_token' => str_repeat('D', 60)
        ]);
        User::factory()->create([
            'name' => 'Mery',
            'api_token' => str_repeat('E', 60)
        ]);
        User::factory()->create([
            'name' => 'Stiven',
            'api_token' => str_repeat('F', 60)
        ]);

        // Act
        $response = $this->apiRequest([
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

}
