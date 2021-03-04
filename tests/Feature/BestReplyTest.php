<?php

namespace Tests\Feature;

use App\Thread;
use App\Reply;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    public function testThreadCreatorMayAnyReplyAsBestReply()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->fresh()->isBest());

        $this->postJson(route('best-replies.store', ['reply' => $replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    public function testOnlyTheThreadCreatorMayMarkReplyAsBest()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->signIn(create(User::class));

        $this->postJson(route('best-replies.store', ['reply' => $replies[1]->id]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    public function testIfBestReplyIsDeletedThenTheThreadIsProperlyUpdatedToReflectThat()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.delete', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
