<?php

namespace Database\Seeders;

use App\Models\Investor;
use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Investor::factory()
            ->has(Organization::factory()->count(3))
            ->count(10)
            ->create();
    }
}
