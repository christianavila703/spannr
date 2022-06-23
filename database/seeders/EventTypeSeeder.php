<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventTypes = [
            'Conference',
            'Competition',
            'Class',
            'Retreat'
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create([
                'name' => $eventType
            ]);
        }
    }
}
