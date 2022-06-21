<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_person()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();

        $person = Person::factory()->create();

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'name' => $person->name,
        ]);

        $this->actingAs($user, 'web')->get(route('people.index'))->assertSee($person->name);
    }
}
