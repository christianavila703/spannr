<?php

namespace Database\Seeders;

use App\Models\FundraisingRound;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
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
            ->hasAttached(
                Person::factory()->count(3),
                ['role' => Person::ORGANIZATION_ROLE_LEADERSHIP]
            )
            ->count(10)
            ->create();
    }
}
