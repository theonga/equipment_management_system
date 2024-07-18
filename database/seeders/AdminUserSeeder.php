<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the super user role
        $superUserRole = Role::firstOrCreate(['name' => 'super_user'], [
    
        ]);

        // Create an admin user with the super user role
        User::create([
            'name' => 'Admin User',
            'email' => 'dev@genesisrealestateafrica.com',
            'password' => Hash::make('7A2345*kycz'),
            'role_id' => $superUserRole->id,
            'company_id' => 1,
        ]);
    }
}