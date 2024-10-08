<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryIds = Category::where('status',0)->orwhere('position',0)->pluck('id')->toArray();
        return [
            // Category..!
            // 'name' => fake()->word(),
            // 'parent_id' => 0,
            // 'position' => 0,
            // 'Image' => 'Images/category/2024-09-29-66f8efa4e0f36.jpeg',
            // 'priority' => fake()->numberBetween(1,10)


            // Sub Category..!
            'name' => fake()->word(),
            'parent_id' => fake()->randomElement($categoryIds),
            'position' => 1,
            'Image' => 'Images/category/2024-09-29-66f8efb710055.png',
            'priority' => fake()->numberBetween(1,10)
        ];
    }
}
