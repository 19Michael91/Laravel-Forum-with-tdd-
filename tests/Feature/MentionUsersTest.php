<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    public function testMentionUsersInReplyAreNotified()
    {
        $jhon = create(User::class, ['name' => 'JhonSmith']);

        $this->signIn($jhon);

        $jane = create(User::class, ['name' => 'JaneSmith']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => '@JaneSmith look at this. Also @FrankDoe'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    public function testItCanFetchAllMentionedUsersStartingWithTheGivenCharacters()
    {
        create(User::class, ['name' => 'JhonSmith']);
        create(User::class, ['name' => 'JhonJackson']);
        create(User::class, ['name' => 'JaneSmith']);

        $results = $this->json('GET', '/api/users', ['name' => 'jhon']);

        $this->assertCount(2, $results->json());
    }
}
