<?php

namespace Database\Factories;

use App\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory {

    protected $model = Tag::class;

    public function definition()
    {
        $tag_for_testing_or_local = [
            'local' => [
                'name' => $this->faker->word(),
            ],
            'testing' => [
                'name' => $this->faker->unique()->word,
            ],
            'production' => [
                'name' => $this->faker->word(),
            ],
        ];

        return $tag_for_testing_or_local[env('APP_ENV')];
    }

}
