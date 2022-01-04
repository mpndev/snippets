<?php

namespace Tests\Feature\API;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.register.store';

    protected $test_verb = 'post';

    /** @test */
    public function guest_can_register()
    {
        // Arrange

        // Act
        $response = $this->apiRequest([], [
            'name' => 'John Doe',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'foo@example.com',
        ]);

        // Assert
        $user = User::find(1);
        $response
            ->assertStatus(201)
            ->assertJson($user->toArray());
    }

    /** @test */
    public function validation_is_ok()
    {
        // Arrange

        // Act
        $response1 = $this->apiRequest();
        $response2 = $this->apiRequest([
            'name' => 'John Doe',
        ]);
        $response3 = $this->apiRequest([
            'password' => 'password',
        ]);
        $response4 = $this->apiRequest([
            'password_confirmation' => 'password',
        ]);
        $response5 = $this->apiRequest([
            'name' => 'John Doe',
            'password' => 'password',
        ]);
        $response6 = $this->apiRequest([
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response7 = $this->apiRequest([
            'name' => 'John Doe',
            'password_confirmation' => 'password',
        ]);
        $response8 = $this->apiRequest([
            'name' => 'John Doe',
            'password' => 'password',
            'password_confirmation' => 'different_password',
        ]);
        $response9 = $this->apiRequest([
            'name' => 'John Doe',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'fooNotAtexample.com',
        ]);

        // Assert
        $response1
            ->assertStatus(400)
            ->assertJson([
                'name' => [
                    'The name field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
            ]);
        $response2
            ->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password field is required.',
                ],
            ]);
        $response3
            ->assertStatus(400)
            ->assertJson([
                'name' => [
                    'The name field is required.',
                ],
                'password' => [
                    'The password confirmation does not match.',
                ],
            ]);
        $response4
            ->assertStatus(400)
            ->assertJson([
                'name' => [
                    'The name field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
            ]);
        $response5
            ->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password confirmation does not match.',
                ],
            ]);
        $response6
            ->assertStatus(400)
            ->assertJson([
                'name' => [
                    'The name field is required.',
                ],
            ]);
        $response7
            ->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password field is required.',
                ],
            ]);
        $response8
            ->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password confirmation does not match.',
                ],
            ]);
        $response9
            ->assertStatus(400)
            ->assertJson([
                'email' => [
                    'The email must be a valid email address.',
                ],
            ]);
    }

    /** @test */
    public function user_cannot_register()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([
            'name' => $user->name,
            'password' => 'password', // $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi,
            'password_confirmation' => 'password',
            'api_token' => $user->api_token,
        ]);

        // Assert
        $response
            ->assertStatus(400)
            ->assertJson([
                'name' => [
                    'The name has already been taken.',
                ],
            ]);
    }

}
