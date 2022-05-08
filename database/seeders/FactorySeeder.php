<?php

namespace Database\Seeders;

use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::factory()
            ->hasAttached(
                Person::factory()->count(2),
                ['role' => Person::ORGANIZATION_ROLE_FOUNDER]
            )
            ->count(10)
            ->create();

        // TODO: Move all the relationships to their own factories for the random from faker??

        Investor::factory()
            ->has(Organization::factory()->count(3))
            ->count(10)
            ->create();
    }
}
