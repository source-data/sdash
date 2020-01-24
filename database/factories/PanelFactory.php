<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Panel;
use Faker\Generator as Faker;

$factory->define(Panel::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'caption'   => $faker->paragraph(5),
        'user_id'   => $faker->numberBetween(2,3),
        'type'      => $faker->word,
        'subtype'   => $faker->word,
        'clicks'    => $faker->randomDigit,
        'downloads' => $faker->randomDigit
    ];
});
