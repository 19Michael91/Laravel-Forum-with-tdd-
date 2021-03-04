<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    public function testItHasOwner()
    {
        $reply = create(Reply::class);

        $this->assertInstanceOf(User::class, $reply->owner);
    }

    public function testKnowsIfItWasJustPublished()
    {
        $reply = create(Reply::class);

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    public function testItCanDetectAllMentionedUsersInTheBody()
    {
        $reply = new Reply([
            'body' => '@JaneSmith wants to talk to @JohnSmith'
        ]);

        $this->assertEquals(['JaneSmith', 'JohnSmith'], $reply->mentionedUsers());
    }

    public function testItWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
    {
        $reply = new Reply([
            'body' => 'Hello @Jane-Smith.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Jane-Smith">@Jane-Smith</a>.',
            $reply->body
        );
    }

    public function testItKnowsIfItTheBestReply()
    {
        $reply = create(Reply::class);

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    public function testReplyBodyIsSanitizedAutomatically()
    {
        $reply = make(Reply::class, [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals('<p>This is okay.</p>', $reply->body);
    }
}
