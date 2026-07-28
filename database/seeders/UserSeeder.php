<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@contoh.id',
            'password' => Hash::make('password123'), // GANTI setelah login pertama
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bendahara Utama',
            'email' => 'bendahara@contoh.id',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
        ]);
    }
}
