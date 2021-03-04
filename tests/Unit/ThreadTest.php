<?php

namespace Tests\Unit;

use App\Thread;
use App\User;
use App\Channel;
use App\Notifications\ThreadWasUpdated;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    public function testThreadHasReplies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    public function testThreadHasCreator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    public function testThreadCanAddReply()
    {
        $this->thread->addReply([
            'body'      => 'FooBar',
            'user_id'   => '1',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    public function testThreadNotifiesAllRegisteredSubscribersWhenReplyIsAdded()
    {
        Notification::fake();

        $this->signIn()
             ->thread
             ->subscribe()
             ->addReply([
                'body'      => 'FooBar',
                'user_id'   => '1',
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    public function testThreadBelongsToChannel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    public function testThreadhasPath()
    {
        $thread = create(Thread::class);

        $this->assertEquals('/threads/' . $thread->channel->slug . '/' . $thread->slug, $thread->path());
    }

    public function testThreadCanBeSubscribedTo()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $thread->subscribe($userId = 1);

        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    public function testThreadCanBeUnsubscribedFrom()
    {
        $thread = create(Thread::class);

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }

    public function testItknowsIfTheAuthenticatedUserIsSubscribedToIt()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    public function testThreadCanCheckIfAuthenticatedUserHasReadAllReplies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        tap(auth()->user(), function($user) use ($thread){
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    public function testThreadsBodyIsSanitizedAutomatically()
    {
        $thread = make(Thread::class, [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals('<p>This is okay.</p>', $thread->body);
    }
}
