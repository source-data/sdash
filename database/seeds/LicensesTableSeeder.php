<?php

use Illuminate\Database\Seeder;
use App\Models\License;

class LicensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $licenses = [
            [
                'code' => 'CC BY 4.0',
                'name' => 'Creative Commons Attribution 4.0 International Public License',
                'url' => 'https://creativecommons.org/licenses/by/4.0/',
            ],
        ];
        $licenseId = 1;
        foreach ($licenses as $license) {
            $license['id'] = $licenseId;
            License::updateOrCreate(['id' => $licenseId], $license);
            $licenseId++;
        }
    }
}
