<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    public function testNotificationIsPreparedWhenSubscribedThreadReceivesNewReplyThatIsNotByCurrentUser()
    {
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id'   => auth()->id(),
            'body'      => 'Some body for test',
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id'   => create(User::class)->id,
            'body'      => 'Some body for test',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    public function testUserCanFetchTheirUnreadNotifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1,
                            $this->getJson(route('profiles.notifications.index', ['user' => auth()->user()->name]))->json());
    }

    public function testUserCanMarkNotificationAsRead()
    {
        create(DatabaseNotification::class);

        tap(auth()->user(), function($user){
            $this->assertCount(1, $user->unreadNotifications);

            $this->delete(route('profiles.notifications.delete', ['user' => $user->name, 'notification' => $user->unreadnotifications->first()->id]));

            $this->assertCount(1, $user->unreadNotifications);
        });
    }
}
