<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('login', 'test_user_1')->first()) {
            User::create([
                'name' => 'Test User 1',
                'email' => 'test1@test.com',
                'login' => 'test_user_1',
                'password' => Hash::make('password1')
            ]);
        }

        User::factory(15)->create();
    }
}
