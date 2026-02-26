<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $title = fake()->streetName() . ' Home';

        return [
            'agent_id'     => Agent::inRandomOrder()->first()?->id,
            'title'        => $title,
            'slug'         => Str::slug($title . '-' . fake()->unique()->numerify('####')),
            'excerpt'      => fake()->sentence(12),
            'description'  => fake()->paragraphs(4, true),
            'price'        => fake()->numberBetween(750_000, 12_000_000),
            'status'       => fake()->randomElement(['for_sale', 'for_rent']),
            'type'         => fake()->randomElement(['house', 'apartment', 'townhouse', 'vacant_land']),
            'special_type' => fake()->optional(0.3)->randomElement(['waterfront', 'penthouse', 'gated_community']),
            'city'         => 'KZN South Coast',
            'suburb'       => fake()->randomElement([
                'Uvongo', 'Shelly Beach', 'Margate', 'Ramsgate',
                'Southbroom', 'Port Shepstone', 'Hibberdene', 'Umzumbe',
            ]),
            'bedrooms'     => fake()->numberBetween(1, 6),
            'bathrooms'    => fake()->numberBetween(1, 4),
            'garages'      => fake()->numberBetween(0, 3),
            'floor_size'   => fake()->numberBetween(60, 650),
            'erf_size'     => fake()->numberBetween(200, 2000),
            'is_visible'   => true,
            'is_exclusive' => fake()->boolean(20),
            'listed_at'    => now()->subDays(rand(0, 60)),
        ];
    }
}
