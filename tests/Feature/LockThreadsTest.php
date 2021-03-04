<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function testNonAdministratorMayNotLockThreads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', ['thread' => $thread]))
             ->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    public function testAdmininstratorCanLockThreads()
    {
        $this->signIn(factory(User::class)->states('administrator')->create());

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', ['thread' => $thread]));

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    public function testAdmininstratorCanUnlockThreads()
    {
        $this->signIn(factory(User::class)->states('administrator')->create());

        $thread = create(Thread::class, ['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.delete', ['thread' => $thread]));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    public function testOnceLockedThreadMayNotReceiveNewReplies()
    {
        $this->signIn();

        $thread = create(Thread::class, ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id(),
        ])->assertStatus(422);
    }
}
