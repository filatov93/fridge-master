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
        $users = [
            User::create([
                'name' => 'Test User 1',
                'email' => 'test1@test.com',
                'login' => 'test_user_1',
                'password' => Hash::make('password1')
            ]),
        ];
        foreach ($users as $user) {
            $user->save();
        }
    }
}
