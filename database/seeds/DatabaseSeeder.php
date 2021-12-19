<?php

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
        factory(User::class)->create([
            'name' => 'mpndev',
            'password' => Hash::make('Smpndev89!'),
        ]);
        $users = factory(User::class, 3)->make();
        $tags = collect([
            factory(Tag::class)->create(['name' => 'foo']),
            factory(Tag::class)->create(['name' => 'bar']),
            factory(Tag::class)->create(['name' => 'baz']),
            factory(Tag::class)->create(['name' => 'fuz']),
            factory(Tag::class)->create(['name' => 'ber']),
        ]);
        foreach($users as $user) {
            $user->generateToken();
            $snippets = factory(Snippet::class, rand(10, 20))->make();
            $snippets->each(function($snippet) use ($user, $tags) {
                $user->addSnippet($snippet);
                $tags->each(function($tag) use ($snippet) {
                    $snippet->addTag($tag);
                });
                $forks = factory(Snippet::class, rand(1, 5))->make();
                $forks->each(function($fork) use ($snippet) {
                    $snippet->addFork($fork);
                });

            });
        }
    }
}
