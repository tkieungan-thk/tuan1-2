<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'status'   => true,
        ]);
        User::create([
            'name'     => 'User1',
            'email'    => 'user1@gmail.com',
            'password' => Hash::make('123456'),
            'status'   => true,
        ]);
        User::create([
            'name'     => 'User2',
            'email'    => 'user2@gmail.com',
            'password' => Hash::make('123456'),
            'status'   => false,
        ]);
    }
}
