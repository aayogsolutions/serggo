<?php

namespace Database\Seeders;

use App\Models\{Admin,Admin_roles};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Admin_roles::create([
            'name' => 'Master Admin',
            'module_access' => '',
        ]);

        Admin::create([
            'name' => 'Master Admin',
            'number' => '7594123810',
            'email' => 'admin@gmail.com',
            'image' => 'def.png',
            'password' => 123456,
            'role_id' => $role->id
        ]);
    }
}
