<?php

namespace Tests\Feature\API;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.settings.update';

    protected $test_verb = 'put';

    /** @test */
    public function user_can_update_his_settings()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([
            'user' => $user->name,
        ], [
            'api_token' => $user->api_token,
            'settings' => json_encode(['theme' => 'darcula']),
            '_method' => 'PUT',
        ]);

        // Assert
        $response
            ->assertStatus(202)
            ->assertJsonFragment(['settings' => '{"theme":"darcula"}']);
    }

    /** @test */
    public function guest_can_not_update_user_settings()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->apiRequest([
            'user' => $user->name,
        ], [
            'settings' => json_encode(['theme' => 'darcula']),
            '_method' => 'PUT',
        ]);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.'
                ],
            ]);
    }

}
