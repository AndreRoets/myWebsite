<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'price' => fake()->numberBetween(100000, 5000000),
            'status' => fake()->randomElement(['for_sale', 'for_rent']),
            'type' => fake()->randomElement(['house', 'apartment', 'townhouse']),
            'city' => fake()->city(),
            'suburb' => fake()->streetName(),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 4),
            'garages' => fake()->numberBetween(0, 3),
            'floor_size' => fake()->numberBetween(50, 500),
            'erf_size' => fake()->numberBetween(200, 2000),
            'excerpt' => fake()->paragraph(2),
            'description' => fake()->paragraphs(3, true),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'listed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}