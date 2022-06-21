<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_organization()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $organization = Organization::factory()->create();

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => $organization->name,
        ]);

        $this->actingAs($user, 'web')->get(route('organizations.index'))->assertSee($organization->name);
    }
}
