<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanSubscribeToThreads()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->post(route('thread.subscription.store', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]));

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    public function testUserCanUnsubscribeFromThreads()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $thread->subscribe();

        $this->delete(route('thread.subscription.delete', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]));

        $this->assertCount(0, $thread->subscriptions);
    }
}
