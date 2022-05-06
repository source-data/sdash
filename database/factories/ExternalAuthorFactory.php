<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExternalAuthor;
use App\User;
use Faker\Generator as Faker;

$factory->define(ExternalAuthor::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'surname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'institution_name' => $faker->sentence(4),
        'department_name' => $faker->sentence(6),
        'orcid'  => $faker->regexify(User::ORCID_REGEX)
    ];
});
