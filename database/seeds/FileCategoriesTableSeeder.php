<?php

use Illuminate\Database\Seeder;

class FileCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('file_categories')->insert([
            ['name' => 'Paper'],
            ['name' => 'Code'],
            ['name' => 'Data'],
            ['name' => 'Methods'],
            ['name' => 'Reagents'],
        ]);
    }
}
