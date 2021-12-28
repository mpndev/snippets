<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Snippet;
use Faker\Generator as Faker;
use \Illuminate\Support\Str;

$factory->define(Snippet::class, function (Faker $faker) {

    $title = $faker->unique()->text(30);
    $snippet_for_testing_or_local = [
        'local' => [
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'description' => $faker->text(220),
            'body' => $faker->randomHtml(1, 3),
            'public' => true,
            'user_id' => 1,
        ],
        'testing' => [
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'description' => 'example description',
            'body' => 'const example = "example peace of code";',
            'public' => true,
            'user_id' => 1,
        ],
        'production' => [
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'description' => $faker->text(220),
            'body' => $faker->randomHtml(1, 3),
            'public' => true,
            'user_id' => 1,
        ],
    ];

    return $snippet_for_testing_or_local[env('APP_ENV')];
});
