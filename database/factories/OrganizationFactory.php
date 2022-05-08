<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
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
            'type' => $this->faker->randomElement(Organization::TYPES),
            'website' => 'https://' . $domainWord . "." . $domainTld,
            'summary' => $this->faker->catchPhrase(),
            'description' => $this->faker->realTextBetween(200, 800),
            'founded_date' => $this->faker->year(),
            'facebook' => $domainWord,
            'twitter' => $domainWord,
            'linkedin' => $domainWord,
            'instagram' => $domainWord,
        ];
    }
}
