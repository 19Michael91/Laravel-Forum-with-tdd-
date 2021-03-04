<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    public function testuserHasProfile()
    {
        $user = create(User::class);

        $this->get('/profiles/' . $user->name)
             ->assertSee($user->name);
    }

    public function testProfilesDisplayAllThreadsCreatedByTheAssociatedUser()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get('/profiles/' . auth()->user()->name)
             ->assertSee($thread->title)
             ->assertSee($thread->body);
    }
}
