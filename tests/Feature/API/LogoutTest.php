<?php

namespace Tests\Feature\API;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected $test_route_name = 'api.login.destroy';

    protected $test_verb = 'delete';

    /** @test */
    public function user_can_log_out()
    {
        $this->withoutExceptionHandling();
        // Arrange
        $user = factory(User::class)->create();

        // Act
        $response = $this->apiRequest([], [
            'api_token' => $user->api_token,
            '_token' => $user->remember_token,
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson(['Successful logged out.'], true);
    }

    /** @test */
    public function guest_cannot_log_out()
    {
        // Arrange

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => [
                    'Unauthenticated.'
                ]
            ]);
    }

}
