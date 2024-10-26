<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brandIds = Brands::where('status',0)->pluck('id')->toArray();
        $categoryIds = Category::where('status',0)->where('position',0)->pluck('id')->toArray();
        $subcategoryIds = Category::where('status',0)->where('position',1)->pluck('id')->toArray();
        $vendorIds = Vendor::where('role','0')->pluck('id')->toArray();
        return [
            'brand_id' => fake()->randomElement($brandIds),
            'brand_name' => fake()->paragraph(),
            // 'admin_id' => 1,
            'vender_id' => fake()->randomElement($vendorIds),
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'image' => '["Images\/productImages\/2024-09-29-66f8f2ab92d25.jpeg","Images\/productImages\/2024-09-29-66f8f319e2aac.jpeg","Images\/productImages\/2024-09-30-66fa9b3221d41.jpg"]',
            'price' => fake()->numberBetween(1,500),
            'variations' => '[{"type":"whit","price":45000,"stock":1},{"type":"black","price":45000,"stock":2}]',
            'tags' => '["'.fake()->word().'","'.fake()->word().'","'.fake()->word().'","'.fake()->word().'"]',
            'tax' => fake()->randomElement([5,12,18,28]),
            'tax_type' => 'percent',
            'status' => 0,
            'attributes' => '["2"]',
            'category_id' => fake()->randomElement($categoryIds),
            'sub_category_id' => fake()->randomElement($subcategoryIds),
            'choice_options' => '[{"name":"choice_2","title":"Color","options":["whit","black"]}]',
            'discount' => fake()->numberBetween(1,50),
            'discount_type' => 'percent',
            'unit' => fake()->randomElement(['pc','kg']),
            'total_stock' => fake()->numberBetween(1,500),
            'total_sale' => fake()->numberBetween(1,500),
            'installation_name' => fake()->word(),
            'installation_charges' => fake()->numberBetween(1,500),
            'installation_description' => fake()->paragraph(),
            'is_featured' => 1,
        ];
    }
}
