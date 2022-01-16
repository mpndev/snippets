<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDarkmodUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.users.darkmod.update';

    protected $test_verb = 'put';

    /** @test */
    public function user_can_update_his_darkmod()
    {
        // Arrange
        $user = User::factory()->create(['darkmod' => false]);

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
        ], [
            'api_token' => $user->api_token,
            'darkmod' => true,
            '_method' => 'PUT',
        ]);

        // Assert
        $response
            ->assertStatus(202)
            ->assertJsonFragment(['darkmod' => true]);
    }

    /** @test */
    public function guest_can_not_update_user_darkmod()
    {
        // Arrange
        $user = User::factory()->create(['darkmod' => false]);

        // Act
        $response = $this->apiRequest([
            'user' => $user->id,
        ], [
            'darkmod' => true,
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
