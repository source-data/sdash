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
        DB::table('users')->insert([[
            'firstname' => 'Terrence',
            'surname' => 'Particle',
            'role' => 'superadmin',
            'institution_name' => 'EMBO - Excellence in Life Sciences',
            'institution_address' => 'Meyerhofstrasse 1, 69117, Heidelberg',
            'department_name' => 'Communications',
            'linkedin' => 'http://linkedin.com',
            'twitter' => 'https://twitter.com',
            'orcid' => '0000-0003-2219-5841',
            'email' => 'embo_it@embo.org',
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
            'email' => 'user@embo.org',
            'password' => Hash::make('password'),
            'created_at' => \date('Y-m-d H:i:s'),
            'updated_at' => \date('Y-m-d H:i:s'),
            'email_verified_at' => \date('Y-m-d H:i:s')
        ],
        [
            'firstname' => 'Chun-San',
            'surname' => 'Wan',
            'role' => 'user',
            'institution_name' => 'Legendary Research Institute',
            'institution_address' => 'Cho Lin Road 54, Taiwan',
            'department_name' => 'Department of Excellence',
            'linkedin' => 'http://linkedin.com',
            'twitter' => 'https://twitter.com',
            'orcid' => '0000-0003-2219-5841',
            'email' => 'csw@embo.org',
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
            'email' => 'bb@embo.org',
            'password' => Hash::make('password'),
            'created_at' => \date('Y-m-d H:i:s'),
            'updated_at' => \date('Y-m-d H:i:s'),
            'email_verified_at' => \date('Y-m-d H:i:s')
        ],
        [
            'firstname' => 'Anthony',
            'surname' => 'Lemmings',
            'role' => 'user',
            'institution_name' => 'Psygnosis',
            'institution_address' => '139 Pingas Street, Paris, Italia',
            'department_name' => 'Development and Software Production',
            'linkedin' => 'http://linkedin.com',
            'twitter' => 'https://twitter.com',
            'orcid' => '0000-0003-2219-5841',
            'email' => 'al@embo.org',
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
            'institution_address' => '139 Pingas Street, Paris, Italia',
            'department_name' => 'School of Oriental and Asiatic Studies',
            'linkedin' => 'http://linkedin.com',
            'twitter' => 'https://twitter.com',
            'orcid' => '0000-0003-2219-5841',
            'email' => 'rp@embo.org',
            'password' => Hash::make('password'),
            'created_at' => \date('Y-m-d H:i:s'),
            'updated_at' => \date('Y-m-d H:i:s'),
            'email_verified_at' => \date('Y-m-d H:i:s')
        ]
        ]);
    }
}
