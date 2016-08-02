<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run()
		{
			DB::table('role')->insert(['role_name' => 'admin']);
			DB::table('role')->insert(['role_name' => 'member']);
		}
}
