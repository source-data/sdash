<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'panel_id'              => $faker->randomDigit,
        'mime_type'             => 'image/jpeg',
        'original_filename'     => $faker->regexify('[A-Za-z0-9]{20}') . '.jpg',
        'filename'              => $faker->regexify('[A-Za-z0-9]{20}') . '.jpg',
        'preview_filename'      => $faker->regexify('[A-Za-z0-9]{20}') . '.jpg',
        'file_size'             => $faker->numberBetween(1000, 50000),
        'is_archived'           => 0
    ];
});
