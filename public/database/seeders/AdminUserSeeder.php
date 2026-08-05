<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    // CHANGE these before running `php artisan db:seed --class=AdminUserSeeder`.
    // The original site shipped with a default admin/changeme login that was
    // never rotated in the SQL dump — do not repeat that here.
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'change-me@zabida.org'],
            [
                'name' => 'ZABIDA Admin',
                'password' => Hash::make('ChangeThisPasswordBeforeSeeding!23'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
