<?php

use Illuminate\Database\Seeder;
use App\Role;
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
        'role_id' => Role::where('name','ex_com')->value('id'),
        'created_at' => now(),
    ]);
    DB::table('positions')->insert([
      'name' => 'vice-chairperson',
      'role_id' => Role::where('name','ex_com')->value('id'),
      'created_at' => now(),
  ]);
  DB::table('positions')->insert([
    'name' => 'secratory',
    'role_id' => Role::where('name','ex_com')->value('id'),
    'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'treasure',
  'role_id' => Role::where('name','ex_com')->value('id'),
  'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'director',
  'role_id' => Role::where('name','highboard')->value('id'),
  'created_at' => now(),
]);
DB::table('positions')->insert([
  'name' => 'volunteer',
  'role_id' => Role::where('name','volunteer')->value('id'),
  'created_at' => now(),
]);
    }
}
