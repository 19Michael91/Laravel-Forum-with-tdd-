<?php

namespace Tests\Feature;

use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    public function testGuestsCanNotFavoriteAnything()
    {
        $this->withExceptionHandling();

        $this->post(route('replies.favorites.store', ['reply' => '1']))
             ->assertRedirect(route('login'));
    }

    public function testAuthenticatedUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorites.store', ['reply' => $reply->id]));

        $this->assertCount(1, $reply->favorites);
    }

    public function testAuthenticatedUserCanUnfavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite(auth()->id());

        $this->delete(route('replies.favorites.delete', ['reply' => $reply->id]));

        $this->assertCount(0, $reply->favorites);
    }

    public function testAuthenticatedUserMayOnlyFavoriteReplyOnce()
    {
        $this->signIn();

        $reply = create(Reply::class);

        try {
            $this->post(route('replies.favorites.store', ['reply' => $reply->id]));
            $this->post(route('replies.favorites.store', ['reply' => $reply->id]));
        } catch (\Exception $e) {
            $this->fail('Did not except to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
