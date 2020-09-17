<?php

use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('seasons')->insert([
        'name' => '2020-2021',
        'startDate' => '2020-08-01',
        'endDate' => '2021-08-01',
        'isActive' => 1,
        'created_at' => now(),
      ]);
      DB::table('seasons')->insert([
        'name' => '2021-2022',
        'startDate' => '2021-08-01',
        'endDate' => '2022-08-01',
        'isActive' => 0,
        'created_at' => now(),
      ]);
    }
}
