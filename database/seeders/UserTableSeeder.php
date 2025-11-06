<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            [
            'name'              => 'System Admin',
            'email'             => 'admin@gmail.com',
            'password'          => Hash::make('12345678'),
            'phone'             => '01',
            'role'              => 'admin',
            'email_verified_at' =>  now(),
        ]);
    }
}
