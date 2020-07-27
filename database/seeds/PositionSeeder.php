<?php

use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('positions')->insert([
        'name' => 'chairperson',
        'created_at' => now(),
    ]);
    DB::table('positions')->insert([
      'name' => 'vice-chairperson',
      'created_at' => now(),
  ]);
  DB::table('positions')->insert([
    'name' => 'secratory',
    'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'treasure',
  'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'director',
  'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'volunteer',
  'created_at' => now(),
]);
    }
}
