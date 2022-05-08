<?php

namespace Database\Seeders;

use App\Models\FundraisingRound;
use App\Models\Investor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FundraisingRoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FundraisingRound::factory()
            ->has(Investor::factory()->count(2))
            ->count(10)
            ->create();
    }
}
