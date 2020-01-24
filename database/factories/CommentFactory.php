<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id'   => $faker->numberBetween(1,3),
        'comment'   => $faker->paragraph(3),
    ];
});
