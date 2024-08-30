<?php

namespace Database\Factories;

use App\Helpers\AddressHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MasterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $password = bcrypt('werter');
        // Діапазони для центральної Європи
        $latitude = fake()->randomFloat(6, 46, 54);  // Широта: від 46° до 54°
        $longitude = fake()->randomFloat(6, 6, 19);  // Довгота: від 6° до 19°

        //$address = AddressHelper::getPlaceId($latitude, $longitude);
        $address = '';
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'age' => fake()->numberBetween(18, 80),
            'speciality_id' => fake()->numberBetween(1, 5),
            'longitude' => $longitude,
            'latitude' => $latitude,
            'password' => $password,
            'description' => fake()->text(200) ,
            'address' => $address,
            'photo' => $this->getImageUrl(),
        ];
    }

    public static function getImageUrl(){
//        $randomPhotoName = fake()->numberBetween(1, 99);
//        $url = 'https://randomuser.me/api/portraits/men/' . $randomPhotoName . '.jpg';
//        $imageContent = file_get_contents($url);
//        $image = fake()->numberBetween(111111, 999999) . '.jpg';
//        \Illuminate\Support\Facades\Storage::disk('public')->put($image, $imageContent);
        $files = Storage::allFiles('public');
        $randomFile = $files[rand(2, count($files) - 1)];
        $filename = str_replace('public/', '', $randomFile);
        return $filename;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
