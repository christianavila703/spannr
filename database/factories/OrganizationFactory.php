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
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(Organization::TYPES),
            'website' => $this->faker->url(),
            'summary' => $this->faker->catchPhrase(),
            'description' => $this->faker->paragraph,
            'founded_date' => $this->faker->year(),
            'facebook' => 'https://www.facebook.com/' . $this->faker->word,
            'twitter' => 'https://www.twitter.com/' . $this->faker->word,
            'linkedin' => 'https://www.linkedin.com/' . $this->faker->word,
            'instagram' => 'https://www.instagram.com/' . $this->faker->word,
        ];
    }
}
