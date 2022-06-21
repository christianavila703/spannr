<?php

namespace Tests\Feature;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_investor()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $investor = Investor::factory()->create();

        $this->assertDatabaseHas('investors', [
            'id' => $investor->id,
            'name' => $investor->name,
        ]);

        $this->actingAs($user, 'web')->get(route('investors.index'))->assertSee($investor->name);
    }
}
