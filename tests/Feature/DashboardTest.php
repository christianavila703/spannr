<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_logs_in_to_dashboard()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/');

        $response->assertStatus(200);
    }
}
