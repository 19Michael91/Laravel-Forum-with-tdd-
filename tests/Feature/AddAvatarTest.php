<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    public function testOnlyMembersCanAddAvatars()
    {
        $this->withExceptionHandling();

        $this->json('post', 'users/{user}/avatar')
             ->assertStatus(401);
    }

    public function testValidAvatarMustBeProvided()
    {
        $this->withExceptionHandling()->signIn();

        $this->json('post', 'users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }

    public function testUserMayAddAvatarToTheirProfile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST',
                    'users/' . auth()->id() . '/avatar',
                    ['avatar' => $file = UploadedFile::fake()->image('avatar.jpg')]);

        $this->assertEquals(Storage::url('avatars/' . $file->hashName()),
                            auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
