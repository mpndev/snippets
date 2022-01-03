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
        // Arrange
        $user = User::factory()->create();

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
    public function guest_can_log_out_as_well_in_order_to_clear_bad_local_storage_data()
    {
        // Arrange

        // Act
        $response = $this->apiRequest();

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson(['Successful logged out.'], true);
    }

}
