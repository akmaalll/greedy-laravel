<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $clientRole = Role::where('slug', 'client')->first();
        $photographerRole = Role::where('slug', 'photographer')->first();

        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Photography',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // 2. Clients
        User::updateOrCreate(
            ['email' => 'client1@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role_id' => $clientRole->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role_id' => $clientRole->id,
            ]
        );

        // 3. Photographers
        User::updateOrCreate(
            ['email' => 'photographer1@example.com'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'role_id' => $photographerRole->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'photographer2@example.com'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'role_id' => $photographerRole->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'photographer3@example.com'],
            [
                'name' => 'Rudi Hermawan',
                'password' => Hash::make('password'),
                'role_id' => $photographerRole->id,
            ]
        );

        $this->command->info('Users created: 1 Admin, 2 Clients, 3 Photographers');
        $this->command->info('All passwords: password');
    }
}
