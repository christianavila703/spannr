<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $jobTitle = $this->faker->jobTitle;

        return [
            'name' => $this->faker->name,
            'byline' => $jobTitle . ' at ' . $this->faker->company,
            'job_type' => $jobTitle,
            'website' => 'https://' . $this->faker->domainName(),
            'bio' => $this->faker->paragraph,
            'facebook' => 'https://www.facebook.com/' . $this->faker->domainWord,
            'twitter' => 'https://www.twitter.com/' . $this->faker->domainWord,
            'linkedin' => 'https://www.linkedin.com/' . $this->faker->domainWord,
            'instagram' => 'https://www.instagram.com/' . $this->faker->domainWord,
            'google_scholar' => 'https://scholar.google.com/citations?user=' . $this->faker->word,
        ];
    }
}
