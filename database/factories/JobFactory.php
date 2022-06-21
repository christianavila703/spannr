<?php

namespace Database\Factories;

use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $owner = $this->faker->randomElement([Organization::factory()->create(), Investor::factory()->create()]);
        $jobCategories = JobCategory::pluck('id')->toArray();

        return [
            'name' => $this->faker->jobTitle(),
            'owner_id' => $owner->id,
            'owner_type' => get_class($owner),
            'posted_date' => Carbon::parse($this->faker->dateTimeBetween('-12 months', 'today'))->format('Y-m-d'),
            'salary' => $this->faker->numberBetween(30000, 150000),
            'status' => $this->faker->randomElement(Job::STATUSES),
            'type' => $this->faker->randomElement(Job::TYPES),
            'description' => $this->faker->realTextBetween(200, 1500),
            'url' => $this->faker->url,
            'category_id' => $this->faker->randomElement($jobCategories)
        ];
    }
}
