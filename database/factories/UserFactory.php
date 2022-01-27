<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'surname'   => $faker->lastName,
        'email'     => $faker->unique()->safeEmail,
        'role'      => $faker->randomElement(['user', 'admin', 'superadmin']),
        'email_verified_at' => now(),
        'institution_name' => $faker->sentence(4),
        'institution_address' => $faker->sentence(6),
        'department_name' => $faker->sentence(5),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'user_slug' => Str::uuid(),
    ];
});
