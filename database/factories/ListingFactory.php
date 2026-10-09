<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition()
    {
        return [
            // The name sets the slug (Listing::setNameAttribute). Names are unique in the table.
            'name' => $this->faker->unique()->company() . ' ' . $this->faker->unique()->numberBetween(1, 99999),
            'type' => Listing::TYPE_CLINIC,
            'status' => Listing::STATUS_PUBLIC,
            'summary' => $this->faker->sentence(),
        ];
    }

    public function doctor(): static
    {
        return $this->state(['type' => Listing::TYPE_DOCTOR]);
    }

    public function pending(): static
    {
        return $this->state(['status' => Listing::STATUS_PENDING]);
    }
}
