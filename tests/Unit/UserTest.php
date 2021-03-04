<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanFetchTheirMostRecentReply()
    {
        $user  = create(User::class);
        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    public function testUserCanDetermineTheirAvatarPath()
    {
        $user = create(User::class);

        $this->assertEquals('/images/avatars/default.jpg', $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals(Storage::url('avatars/me.jpg'), $user->avatar_path);
    }
}
