<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UpdateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn();
    }

    public function testThreadRequiresTitleAndBodyToBeUpdated()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch(route('threads.update', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]),
                     ['title' => 'Changed'])
             ->assertSessionHasErrors('body');

        $this->patch(route('threads.update', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]),
                     ['body' => 'Changed body'])
             ->assertSessionHasErrors('title');
    }

    public function testUnauthorizedUsersMayNotUpdateThreads()
    {
        $thread = create(Thread::class, ['user_id' => create(User::class)->id]);

        $this->patch(route('threads.update', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]),
                     [])
             ->assertStatus(403);
    }

    public function testThreadCanBeUpdatedByItsCreator()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch(route('threads.update', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]),
                     [
                         'title' => 'Changed',
                         'body' => 'Changed body',
                     ]);

        tap($thread->fresh(), function($thread){
            $this->assertEquals('Changed', $thread->fresh()->title);
            $this->assertEquals('Changed body', $thread->fresh()->body);
        });
    }
}
