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
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(Investor::TYPES),
            'website' => 'https://' . $this->faker->domainName(),
            'summary' => $this->faker->catchPhrase(),
            'facebook' => 'https://www.facebook.com/' . $this->faker->domainWord,
            'twitter' => 'https://www.twitter.com/' . $this->faker->domainWord,
            'linkedin' => 'https://www.linkedin.com/' . $this->faker->domainWord,
            'instagram' => 'https://www.instagram.com/' . $this->faker->domainWord,
        ];
    }
}
