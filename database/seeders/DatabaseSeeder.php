<?php

namespace Database\Seeders;

use App\Models\Attributes;
use App\Models\Brands;
use App\Models\Category;
use App\Models\User;
use App\Models\Products;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Database\Seeders\{
    MasterAdminSeeder,
    BusinessSettingSeeder,
    ProductSeeder
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Attributes::factory(20)->create();
        // Brands::factory(20)->create();
        // Category::factory(20)->create();
        // Vendor::factory(30)->create();
        Products::factory(30)->create();
        
        $this->call([
            // MasterAdminSeeder::class,
            // ProductSeeder::class,
            // PaymentGatewaysSeeder::class
        ]);
    }
}
