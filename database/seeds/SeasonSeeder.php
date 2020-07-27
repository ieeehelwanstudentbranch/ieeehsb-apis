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
        'created_at' => now(),
      ]);
      DB::table('seasons')->insert([
        'name' => '2020-2021',
        'created_at' => now(),
      ]);
    }
}
