<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('roles')->insert([
        'name' => 'ex_com',
        'created_at' => now(),
      ]);
      DB::table('roles')->insert([
        'name' => 'highboard',
        'created_at' => now(),
      ]);
      DB::table('roles')->insert([
        'name' => 'volunteer',
        'created_at' => now(),
      ]);
    }
}
