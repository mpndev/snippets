<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Tag;
use Faker\Generator as Faker;

$factory->define(Tag::class, function (Faker $faker) {

    $tag_for_testing_or_local = [
        'local' => [
            'name' => $faker->word(),
        ],
        'testing' => [
            'name' => $faker->unique()->word,
        ],
        'production' => [
            'name' => $faker->word(),
        ],
    ];

    return $tag_for_testing_or_local[env('APP_ENV')];
});
