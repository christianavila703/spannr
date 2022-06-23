<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->company();
        $slug = Str::of($name)->slug('-');

        $startDate = $this->faker->dateTimeThisYear('+24 months')->format('Y-m-d');
        $endDate = Carbon::parse($startDate)->addDays($this->faker->randomDigit())->format('Y-m-d');

        $imageFile = file_get_contents('http://placekitten.com/512/256');
        $fileName = $slug . ".jpg";

        $file = '/tmp/' . $slug;
        file_put_contents($file, $imageFile);
        $uploaded_file = new UploadedFile($file, $fileName);
        Storage::putFileAs('/public/images/events', $uploaded_file, $fileName);

        return [
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $this->faker->optional()->randomElement([$endDate]),
            'website' => $this->faker->url(),
            'description' => $this->faker->realTextBetween(200, 400),
            'image' => $fileName,
        ];
    }
}
