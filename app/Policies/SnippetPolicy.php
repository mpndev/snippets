<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Snippet;
use Illuminate\Auth\Access\HandlesAuthorization;

class SnippetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any snippets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the snippet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Snippet  $snippet
     * @return mixed
     */
    public function view(User $user, Snippet $snippet)
    {
        //
    }

    /**
     * Determine whether the user can create snippets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the snippet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Snippet  $snippet
     * @return mixed
     */
    public function update(User $user, Snippet $snippet)
    {
        return (int) $snippet->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the snippet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Snippet  $snippet
     * @return mixed
     */
    public function delete(User $user, Snippet $snippet)
    {
        return (int) $snippet->user_id === (int) $user->id;
    }
}
