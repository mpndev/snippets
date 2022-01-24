<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.login.store';

    protected $test_verb = 'post';

    /** @test */
    public function guest_can_log_in()
    {
        // Arrange
        $user = User::factory()->create([
            'remember_token' => null,
            'api_token' => null,
        ]);
        $this->assertGuest();

        // Act
        $response = $this->apiRequest([], [
            'email' => $user->email,
            'password' => 'password', // $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi,
        ]);

        // Assert
        $api_token = $user->fresh()->api_token;
        $response
            ->assertStatus(200)
            ->assertJson($user->fresh()->with(['snippets', 'favoriteSnippets'])->first()->toArray())
            ->assertSessionDoesntHaveErrors();
        $this->assertAuthenticated();
        $this->assertEquals(60, strlen($api_token));
    }

    /** @test */
    public function user_cannot_log_in()
    {
        // Arrange
        $user = User::factory()->create([
            'remember_token' => null,
        ]);

        // Act
        $response = $this->apiRequest([], [
            'email' => $user->email,
            'password' => 'password', // $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi,
            'api_token' => $user->api_token
        ]);

        // Assert
        $api_token = $user->api_token;
        $new_api_token = $user->fresh()->api_token;
        $this->assertEquals($api_token, $new_api_token);
        $response->assertSessionDoesntHaveErrors(['title, password'])
            ->assertStatus(200)
            ->assertJson($user->fresh()->with(['snippets', 'favoriteSnippets'])->first()->toArray());
        $this->assertAuthenticated();
    }

    /** @test */
    public function email_is_required()
    {
        // Arrange

        // Act
        $response = $this->apiRequest([], [
            'password' => 'password', // $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'email' => [
                    'The email field is required.'
                ]
            ]);
    }

    /** @test */
    public function password_is_required()
    {
        // Arrange
        $user = User::factory()->create([
            'remember_token' => null,
            'api_token' => null,
        ]);

        // Act
        $response = $this->apiRequest([], [
            'email' => $user->email,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'password' => [
                    'The password field is required.'
                ]
            ]);
    }

    /** @test */
    public function user_cannot_log_in_with_wrong_password()
    {
        // Arrange
        $user = User::factory()->create([
            'remember_token' => null,
            'api_token' => null,
        ]);

        // Act
        $response = $this->apiRequest([], [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'email' => [
                    'These credentials do not match our records.'
                ]
            ]);
    }

}
