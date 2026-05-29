<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $username = env('ADMIN_USERNAME', 'admin');
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        $user = User::query()
            ->where('username', $username)
            ->orWhere('email', $email)
            ->first() ?? new User;

        $user->fill([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'username' => $username,
            'email' => $email,
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ])->save();
    }
}
