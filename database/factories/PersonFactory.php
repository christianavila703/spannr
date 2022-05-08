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
        $domainWord = $this->faker->domainWord;
        $domainTld = $this->faker->tld;

        return [
            'name' => $this->faker->name,
            'byline' => $jobTitle . ' at ' . $this->faker->company,
            'job_type' => $jobTitle,
            'website' => 'https://' . $domainWord . "." . $domainTld,
            'bio' => $this->faker->realTextBetween(200, 800),
            'facebook' => $domainWord,
            'twitter' => $domainWord,
            'linkedin' => $domainWord,
            'instagram' => $domainWord,
            'google_scholar' => $domainWord,
        ];
    }
}
