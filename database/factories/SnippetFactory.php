<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Snippet;
use Faker\Generator as Faker;

$factory->define(Snippet::class, function (Faker $faker) {

    $snippet_for_testing_or_local = [
        'local' => [
            'title' => $faker->text(30),
            'description' => $faker->text(220),
            'body' => $faker->randomHtml(1, 3),
            'user_id' => 1,
        ],
        'testing' => [
            'title' => 'example snippet title',
            'description' => 'example description',
            'body' => 'const example = "example peace of code";',
            'user_id' => 1,
        ],
        'production' => [
        'title' => $faker->text(30),
        'description' => $faker->text(220),
        'body' => $faker->randomHtml(1, 3),
        'user_id' => 1,
    ],
    ];

    return $snippet_for_testing_or_local[env('APP_ENV')];
});
