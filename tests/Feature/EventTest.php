<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_event()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $event = Event::factory()->create();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => $event->name,
        ]);

        $this->actingAs($user, 'web')->get(route('events.index'))->assertSee($event->name);
    }
}
