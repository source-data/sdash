<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'firstname' => 'Terrence',
                'surname' => 'Particle',
                'role' => 'superadmin',
                'institution_name' => 'EMBO - Excellence in Life Sciences',
                'institution_address' => 'Meyerhofstrasse 1, 69117, Heidelberg',
                'department_name' => 'Communications',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'superadmin@example.org',
                'password' => Hash::make('superadmin'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ],
            [
                'firstname' => 'Joseph',
                'surname' => 'Matrix',
                'role' => 'admin',
                'institution_name' => 'EMBO - Excellence in Life Sciences',
                'institution_address' => 'Meyerhofstrasse 1, 69117, Heidelberg',
                'department_name' => 'SourceData',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'user@example.org',
                'password' => Hash::make('password'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ],
            [
                'firstname' => 'Hasiao-Chun',
                'surname' => 'Wan',
                'role' => 'user',
                'institution_name' => 'Legendary Research Institute',
                'institution_address' => 'Example Road 54, Taiwan',
                'department_name' => 'Department of Excellence',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'csw@example.org',
                'password' => Hash::make('password'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ],
            [
                'firstname' => 'Bosco',
                'surname' => 'Bacardi',
                'role' => 'user',
                'institution_name' => 'Elephant University',
                'institution_address' => '52 Moustache Street, Paris, France',
                'department_name' => 'Department of Science',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'bb@example.org',
                'password' => Hash::make('password'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ],
            [
                'firstname' => 'Anthony',
                'surname' => 'Lemmings',
                'role' => 'user',
                'institution_name' => 'Lemmings Institute',
                'institution_address' => '139 Example Street, Paris, Italia',
                'department_name' => 'Development and Software Production',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'al@example.org',
                'password' => Hash::make('password'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ],
            [
                'firstname' => 'Rodrigo',
                'surname' => 'Potato',
                'role' => 'user',
                'institution_name' => 'Very Good University',
                'institution_address' => '139 Example Street, Paris, Italia',
                'department_name' => 'SOAS',
                'linkedin' => 'http://linkedin.com',
                'twitter' => 'https://twitter.com',
                'orcid' => '0000-0003-2219-5841',
                'email' => 'rp@example.org',
                'password' => Hash::make('password'),
                'created_at' => \date('Y-m-d H:i:s'),
                'updated_at' => \date('Y-m-d H:i:s'),
                'email_verified_at' => \date('Y-m-d H:i:s')
            ]
        ]);
    }
}
