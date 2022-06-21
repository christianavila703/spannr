<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_job()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $job = Job::factory()->create();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'name' => $job->name,
        ]);

        $this->actingAs($user, 'web')->get(route('jobs.index'))->assertSee($job->name);
    }
}
