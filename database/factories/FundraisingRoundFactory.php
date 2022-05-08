<?php

namespace Database\Factories;

use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FundraisingRound>
 */
class FundraisingRoundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'organization_id' => Organization::factory(),
            'round' => $this->faker->randomElement(FundraisingRound::ROUNDS),
            'date' => Carbon::parse($this->faker->dateTimeBetween('-12 months', 'today'))->format('Y-m-d'),
            'amount_raised' => $this->faker->numberBetween(50000, 100000000),
            'announcement_url' => $this->faker->url,
            'description' => $this->faker->realTextBetween(300, 900),
            'valuation' => $this->faker->numberBetween(100000, 10000000),
        ];
    }
}
