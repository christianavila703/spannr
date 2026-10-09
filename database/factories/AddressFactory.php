<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'name' => $this->faker->streetAddress(),
            'street' => $this->faker->streetAddress(),
            'city' => 'Miami',
            'state' => 'Florida',
            'state_code' => 'FL',
            'country' => 'USA',
            'country_code' => 'us',
            'postal_code' => '33127',
            'latitude' => '25.7617',
            'longitude' => '-80.1918',
            'primary' => 1,
        ];
    }

    public function in(string $city, string $state, string $stateCode): static
    {
        return $this->state(['city' => $city, 'state' => $state, 'state_code' => $stateCode]);
    }
}
