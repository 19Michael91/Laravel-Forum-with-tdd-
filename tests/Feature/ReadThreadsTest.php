<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use App\Reply;
use App\Channel;
use App\Visits;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    public function testUserCanViewAllThreads()
    {
        $this->get('/threads')->assertSee($this->thread->title);
    }

    public function testUserCanReadeSingleThread()
    {
        $this->get($this->thread->path())->assertSee($this->thread->title);
    }

    public function testUserCanFilterThreadsAccordingToChannel()
    {
        $channel            = create(Channel::class);
        $threadNotInChannel = create(Thread::class);
        $threadInChannel    = create(Thread::class, ['channel_id' => $channel->id]);

        $this->get('/threads/' . $channel->slug)
             ->assertSee($threadInChannel->title)
             ->assertDontSee($threadNotInChannel->title);
    }

    public function testUserCanFilterThreadsByAnyusername()
    {
        $this->signIn(create(User::class, ['name' => 'JhonDoe']));

        $threadByJhon    = create(Thread::class, ['user_id' => auth()->id()]);
        $threadNotByJhon = create(Thread::class);

        $this->get('threads?by=JhonDoe')
             ->assertSee($threadByJhon->title)
             ->assertDontSee($threadNotByJhon->title);
    }

    public function testUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    public function testUserCanFilterThreadsByThoseThatAreUnanswer()
    {
        $thread = create(Thread::class);
        $reply  = create(Reply::class, ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();
        $this->assertCount(1, $response['data']);
    }

    public function testUserCanRequestAllRepliesForGivenThread()
    {
        $thread   = create(Thread::class);
        $reply    = create(Reply::class, ['thread_id' => $thread->id]);
        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(1, $response['data']);
        $this->assertEquals(1, $response['total']);
    }

    public function testWeRecordNewVisitEachTimeTheThreadIsRead()
    {
        $thread = create(Thread::class);

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
