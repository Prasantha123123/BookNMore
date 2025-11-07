<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            ['name' => 'Photocopy'],
            ['name' => 'Printout'],
            ['name' => 'Binding'],
            ['name' => 'Laminating'],
        ]);
    }
}