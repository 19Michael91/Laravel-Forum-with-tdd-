<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    public function testOnlyMembersCanAddAvatars()
    {
        $this->withExceptionHandling();

        $user = create(User::class);

        $this->json('POST',
                            route('user.avatar.store', ['user' => $user->name]))
             ->assertStatus(401);
    }

    public function testValidAvatarMustBeProvided()
    {
        $this->withExceptionHandling()->signIn();

        $this->json('POST',
                            route('user.avatar.store', ['user' => auth()->user()->name]),
                            ['avatar' => 'not-an-image'])
             ->assertStatus(422);
    }

    public function testUserMayAddAvatarToTheirProfile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST',
                            route('user.avatar.store', ['user' => auth()->user()->name]),
                            ['avatar' => $file = UploadedFile::fake()->image('avatar.jpg')]);

        $this->assertEquals(Storage::url('avatars/' . $file->hashName()),
                            auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
