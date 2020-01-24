<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ModelsImage;
use Faker\Generator as Faker;

$factory->define(ModelsImage::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'caption'   => $faker->paragraph(5),
        'image_id'  => $faker->randomDigit,
        'user_id'   => $faker->randomDigit
    ];
});
