<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function testItIncrementsThreadsScoreEachTimeItIsRead()
    {
        $this->assertEmpty($this->trending->get());

        $thread = create(Thread::class);

        $this->get(route('threads.show', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]));

        $this->assertCount(1, $this->trending->get());

        $this->assertEquals($thread->title, $this->trending->get()[0]->title);
    }
}
