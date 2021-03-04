<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use App\Reply;
use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanCreateNewForumThreads()
    {
        $this->signIn();

        $thread = make(Thread::class);

        $this->post(route('threads.store'), $thread->toArray());

        $this->get($thread->path())
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    public function testNewUsersMustFirstConfirmTheirEmailAddressBeforeCreatingThreads()
    {
        $user = factory(User::class)->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make(Thread::class);

        $this->post(route('threads.index'), $thread->toArray())
             ->assertRedirect(route('threads.index'))
             ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    public function testGuestsCannotCreateThread()
    {
        $this->withExceptionHandling();

        $this->post(route('threads.index'))
            ->assertRedirect(route('login'));

        $this->get(route('threads.create'))
            ->assertRedirect(route('login'));
    }

    public function testThreadRequiresTitle()
    {
        $this->withExceptionHandling();

        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function testThreadRequiresBody()
    {
        $this->withExceptionHandling();

        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    public function testThreadRequiresValidChannel()
    {
        $this->withExceptionHandling();

        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');
    }

    public function testThreadRequiresUniqueSlug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Test Title']);

        $this->assertEquals($thread->fresh()->slug, 'test-title');

        $thread = $this->postJson(route('threads.store'), $thread->toArray())->json();

        $this->assertEquals('test-title-' . $thread['id'], $thread['slug']);
    }

    public function testThreadWithTitleThatEndsInNumberShouldGenerateTheProperSlug()
    {
        $this->signIn();

        $thread = create(Thread::class, [
            'title' => 'Some Title 24',
        ]);

        $thread = $this->postJson(route('threads.store'), $thread->toArray())->json();

        $this->assertEquals('some-title-24-' . $thread['id'], $thread['slug']);
    }

    public function testAuthorizredUsersCanDeleteThreads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());

    }

    public function testUnauthorizedUsersMayNotDeleteThreads()
    {
        $this->withExceptionHandling();

        $thread = create(Thread::class);

        $this->delete($thread->path())
            ->assertRedirect(route('login'));

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);
    }

    public function publishThread($overrides = [])
    {
        $this->signIn();

        $thread = make(Thread::class, $overrides);

        return $this->post(route('threads.index'), $thread->toArray());
    }
}
