<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        if ($admin) {
            $admin->update([
                'password' => Hash::make('password')
            ]);
            $this->command->info('Password admin berhasil di-reset menjadi: password');
        } else {
            $this->command->error('User admin@example.com tidak ditemukan!');
        }
    }
}
