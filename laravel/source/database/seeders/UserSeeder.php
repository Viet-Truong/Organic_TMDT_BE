<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'password' => bcrypt('123456a'),
            'email' => 'john.doe@example.com',
            'role' => User::BUYER,
            'status' => User::STATUS_ACTIVE,
            'name' => 'John Doe',
            'dob' => '1990-01-01',
            'card_id' => 'ID123456',
            'phone_number' => '123456789',
            'address' => '123 Main St, City',
            'gender' => 'male',
            'avatar' => 'avatar.jpg',
            'verification_token' => null,
        ]);

        User::create([
            'password' => bcrypt('123456a'),
            'email' => 'admin@example.com',
            'role' => User::SELLER,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Admin User',
            'dob' => '1985-05-15',
            'card_id' => 'ID654321',
            'phone_number' => '987654321',
            'address' => '456 Admin Blvd, City',
            'gender' => 'female',
            'avatar' => 'admin_avatar.jpg',
            'verification_token' => null,
        ]);

    }
}
