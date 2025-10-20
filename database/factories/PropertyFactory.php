<?php

public function definition(): array
{
    $title = fake()->streetName().' Home';
    $images = [];
    for ($i=0;$i<rand(3,6);$i++) { $images[] = 'properties/sample'.rand(1,6).'.webp'; }

    return [
        'title' => $title,
        'slug' => Str::slug($title.'-'.fake()->unique()->numerify('####')),
        'reference' => strtoupper(fake()->bothify('HF-#####')),
        'price' => fake()->numberBetween(750000, 12000000),
        'currency' => 'ZAR',
        'city' => 'Umhlanga',
        'suburb' => fake()->randomElement(['La Lucia','Prestondale','Sunningdale']),
        'province' => 'KwaZulu-Natal',
        'country' => 'South Africa',
        'bedrooms' => fake()->numberBetween(1,6),
        'bathrooms' => fake()->numberBetween(1,5),
        'garages' => fake()->numberBetween(0,3),
        'floor_size' => fake()->numberBetween(60, 650),
        'erf_size' => fake()->numberBetween(200, 1500),
        'type' => fake()->randomElement(['house','apartment','townhouse']),
        'status' => 'for_sale',
        'excerpt' => fake()->sentence(12),
        'description' => fake()->paragraphs(4, true),
        'images' => $images,
        'hero_image' => $images[0] ?? null,
        'lat' => -29.7270 + fake()->randomFloat(4, -0.1, 0.1),
        'lng' => 31.0649 + fake()->randomFloat(4, -0.1, 0.1),
        'is_featured' => fake()->boolean(25),
        'listed_at' => now()->subDays(rand(0, 45)),
    ];
}
