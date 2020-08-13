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
        'name' => '2019-2020',
        'startDate' => '2019-07-01',
        'endDate' => '2020-07-28',
        'isActive' => 1,
        'created_at' => now(),
      ]);
      DB::table('seasons')->insert([
        'name' => '2020-2021',
        'startDate' => '2020-07-28',
        'endDate' => '2021-07-01',
        'isActive' => 0,
        'created_at' => now(),
      ]);
    }
}
