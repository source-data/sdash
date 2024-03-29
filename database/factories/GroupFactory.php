<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name'          => $faker->sentence(3),
        'description'   => $faker->sentence(10),
        'url'           => $faker->url()
    ];
});
