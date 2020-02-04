<?php

use Illuminate\Database\Seeder;
use App\Snippet;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $snippets = factory(Snippet::class, 8)->create();
        foreach($snippets as $snippet) {
            $child_snippet = factory(Snippet::class, rand(0, 1))->make();
            if ($child_snippet->count()) {
                $snippet->forks()->saveMany($child_snippet);
                foreach($snippet->forks as $fork) {
                    $fork_children = factory(Snippet::class, rand(0, 3))->make();
                    $fork->forks()->saveMany($fork_children);
                }
            }
        }
    }
}
