<?php

namespace Database\Seeders;

use App\Tag;
use App\User;
use App\Snippet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'mpndev',
            'password' => Hash::make('Smpndev89!'),
        ]);
        $users = User::factory()->count(3)->make();
        $tags = collect([
            Tag::factory()->create(['name' => 'foo']),
            Tag::factory()->create(['name' => 'bar']),
            Tag::factory()->create(['name' => 'baz']),
            Tag::factory()->create(['name' => 'fuz']),
            Tag::factory()->create(['name' => 'ber']),
        ]);
        foreach($users as $user) {
            $user->generateToken();
            $snippets = Snippet::factory()->count(rand(10, 20))->make();
            $snippets->each(function($snippet) use ($user, $tags) {
                $user->addSnippet($snippet);
                $tags->each(function($tag) use ($snippet) {
                    $snippet->addTag($tag);
                });
                $forks = Snippet::factory()->count(rand(1, 5))->make();
                $forks->each(function($fork) use ($snippet) {
                    $snippet->addFork($fork);
                });

            });
        }
    }
}
