<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserHasProfile()
    {
        $user = create(User::class);

        $this->get(route('profiles.show', ['user' => $user->name]))
             ->assertSee($user->name);
    }

    public function testProfilesDisplayAllThreadsCreatedByTheAssociatedUser()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get(route('profiles.show', ['user' => auth()->user()->name]))
             ->assertSee($thread->title)
             ->assertSee($thread->body);
    }
}
