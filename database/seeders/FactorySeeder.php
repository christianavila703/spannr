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
            ->count(50)
            ->create();

        Investor::factory()
            ->count(50)
            ->create();

        Person::factory()
            ->count(50)
            ->create();
    }
}
