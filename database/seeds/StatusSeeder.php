<?php

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
          'name' => 'activated',
          'created_at' => now(),
      ]);
        DB::table('statuses')->insert([
          'name' => 'deactivated',
          'created_at' => now(),
      ]);
        DB::table('statuses')->insert([
          'name' => 'pending',
          'created_at' => now(),
      ]);
        DB::table('statuses')->insert([
            'name' => 'delivered',
            'created_at' => now(),
        ]);
        DB::table('statuses')->insert([
          'name' => 'accepted',
          'created_at' => now(),
      ]);
        DB::table('statuses')->insert([
          'name' => 'disapproved',
          'created_at' => now(),
      ]);

        
    }
}
