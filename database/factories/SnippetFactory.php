<?php

namespace Database\Factories;

use App\Snippet;
use Illuminate\Database\Eloquent\Factories\Factory;
use \Illuminate\Support\Str;

class SnippetFactory extends Factory {

    protected $model = Snippet::class;

    public function definition()
    {
        $title = $this->faker->unique()->text(30);
        $snippet_for_testing_or_local = [
            'local' => [
                'title' => $title,
                'slug' => Str::slug($title, '-'),
                'description' => $this->faker->text(220),
                'body' => $this->faker->randomHtml(1, 3),
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
                'description' => $this->faker->text(220),
                'body' => $this->faker->randomHtml(1, 3),
                'public' => true,
                'user_id' => 1,
            ],
        ];

        return $snippet_for_testing_or_local[env('APP_ENV')];
    }

}
