<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Thread $thread)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }

    public function delete(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }

    public function restore(User $user, Thread $thread)
    {
        //
    }

    public function forceDelete(User $user, Thread $thread)
    {
        //
    }
}
