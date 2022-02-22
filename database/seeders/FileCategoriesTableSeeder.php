<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FileCategory;

class FileCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileCategoryNames = [
            'Paper',
            'Code',
            'Data',
            'Methods',
            'Reagents',
            'Repository',
            'Other',
        ];
        $fileCategoryId = 1;
        foreach ($fileCategoryNames as $fileCategoryName) {
            $fileCategory = [
                'id' => $fileCategoryId,
                'name' => $fileCategoryName,
            ];
            FileCategory::updateOrCreate(['id' => $fileCategoryId], $fileCategory);
            $fileCategoryId++;
        }
    }
}
