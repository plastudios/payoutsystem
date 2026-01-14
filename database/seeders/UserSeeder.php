<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Author User',
            'email' => 'author@test.com',
            'password' => Hash::make('password'),
            'role' => 'author'
        ]);

        User::create([
            'name' => 'Checker User',
            'email' => 'checker@test.com',
            'password' => Hash::make('password'),
            'role' => 'checker'
        ]);

        User::create([
            'name' => 'Maker User',
            'email' => 'maker@test.com',
            'password' => Hash::make('password'),
            'role' => 'maker'
        ]);
    }
}
