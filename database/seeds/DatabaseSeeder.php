<?php

use App\User;
use App\Snippet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class, 3)->create();
        $snippets = factory(Snippet::class, 8)->create(['user_id' => $users[rand(0, 2)]]);
        foreach($snippets as $snippet) {
            $child_snippet = factory(Snippet::class, rand(0, 1))->make(['user_id' => $users[rand(0, 2)]]);
            if ($child_snippet->count()) {
                $snippet->forks()->saveMany($child_snippet);
                foreach($snippet->forks as $fork) {
                    $fork_children = factory(Snippet::class, rand(0, 3))->make(['user_id' => $users[rand(0, 2)]]);
                    $fork->forks()->saveMany($fork_children);
                }
            }
        }
    }
}
