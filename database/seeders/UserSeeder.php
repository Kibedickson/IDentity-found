<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => config('auth.admin_name'),
            'email' => config('auth.admin_email'),
            'phone' => fake()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => bcrypt(config('auth.default_password')),
            'remember_token' => Str::random(10),
        ]);

        $admin->assignRole('Super Admin');
    }
}
