<?php

namespace Tests\Feature;

use App\Thread;
use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    public function testUnauthenticatedUsersMayNotAddReplies()
    {
        $this->withExceptionHandling();

        $thread = create(Thread::class);
        $reply  = create(Reply::class);

        $this->post($thread->path() . '/replies', $reply->toArray())->assertRedirect('/login');
    }

    public function testAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->signIn();

        $thread = create(Thread::class);
        $reply  = make(Reply::class);

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    public function testReplyRequiresBody()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);
        $reply  = make(Reply::class, ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
             ->assertSessionHasErrors('body');
    }

    public function testUnauthorizedUsersCannotDeleteReplies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->delete('/replies/' . $reply->id)
             ->assertRedirect('/login');

        $this->signIn()
             ->delete("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    public function testAuthorizedUsersCanDeleteReplies()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $this->delete('/replies/' . $reply->id)->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    public function testAuthorizedUsersCanUpdateReplies()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $updatedReply = 'You been changed.';

        $this->patch('/replies/' . $reply->id, ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

    public function testUnauthorizedUsersCannotUpdateReplies()
    {
        $this->withExceptionHandling();

        $reply = create(Reply::class);

        $this->patch('/replies/' . $reply->id)
             ->assertRedirect('/login');

        $this->signIn()
             ->patch("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    public function testRepliesThatContainSpamMayNotBeCreated()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);
        $reply  = make(Reply::class, [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
             ->assertStatus(422);
    }

    public function testUsersMayOnlyReplyMaximumOfOncePerMinute()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);
        $reply  = make(Reply::class, [
            'body' => 'My Simple Reply'
        ]);

        $this->post($thread->path() . '/replies', $reply->toArray())
             ->assertStatus(201);

         $this->post($thread->path() . '/replies', $reply->toArray())
              ->assertStatus(422);
    }
}
