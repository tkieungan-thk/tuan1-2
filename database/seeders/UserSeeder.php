<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name"=> "Admin",
            "email"=> "admin@gmail.com",
            "password"=> Hash::make("admin123"),
            "role"=>"admin",
            "is_active" => true,
        ]);
        User::create([
            "name"=> "User1",
            "email"=> "user1@gmail.com",
            "password"=> Hash::make("123456"),
            "role"=>"user",
            "is_active" => true,
        ]);
        User::create([
            "name"=> "User2",
            "email"=> "user2@gmail.com",
            "password"=> Hash::make("123456"),
            "role"=>"user",
            "is_active" => false,
        ]);
    }
}