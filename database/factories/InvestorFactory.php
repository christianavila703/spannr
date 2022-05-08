<?php

namespace Database\Factories;

use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Investor>
 */
class InvestorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $domainWord = $this->faker->domainWord;
        $domainTld = $this->faker->tld;

        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(Investor::TYPES),
            'website' => 'https://' . $domainWord . "." . $domainTld,
            'summary' => $this->faker->catchPhrase(),
            'description' => $this->faker->realTextBetween(200, 800),
            'facebook' => $domainWord,
            'twitter' => $domainWord,
            'linkedin' => $domainWord,
            'instagram' => $domainWord,
        ];
    }
}
