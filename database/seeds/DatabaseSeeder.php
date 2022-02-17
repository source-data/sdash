<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(PanelsTableSeeder::class);
        $this->call(ImagesTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(FileCategoriesTableSeeder::class);
        $this->call(LicensesTableSeeder::class);
    }
}
