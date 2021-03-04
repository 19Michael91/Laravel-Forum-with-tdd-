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

        $this->post('/replies/1/favorites')
             ->assertRedirect('/login');
    }

    public function testAuthenticatedUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    public function testAuthenticatedUserCanUnfavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite(auth()->id());

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0, $reply->favorites);
    }

    public function testAuthenticatedUserMayOnlyFavoriteReplyOnce()
    {
        $this->signIn();

        $reply = create(Reply::class);

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not except to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
