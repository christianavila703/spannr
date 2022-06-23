<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::factory()
            ->count(10)
            ->hasAttached(Organization::factory()->count(2))
            ->hasAttached(Investor::factory()->count(2))
            ->hasAttached(Person::factory()->count(4))
            ->hasAttached(EventType::factory()->count(1))
            ->create();
    }
}
