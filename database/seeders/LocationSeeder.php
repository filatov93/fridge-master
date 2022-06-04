<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            [
                'name' => 'Уилмингтон (Северная Каролина)'
            ],
            [
                'name' => 'Портленд (Орегон)'
            ],
            [
                'name' => 'Торонто'
            ],
            [
                'name' => 'Варшава'
            ],
            [
                'name' => 'Валенсия'
            ],
            [
                'name' => 'Шанхай'
            ],
        ]);
    }
}
