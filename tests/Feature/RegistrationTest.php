<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use App\Mail\PleaseConfirmYourEmail;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    public function testConfirmationEmailIsSentUponRegistration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name'                  => 'Jhon',
            'email'                 => 'jhon@example.com',
            'password'              => 'foobar',
            'password_confirmation' => 'foobar',
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    public function testUserCanFullyConfirmTheirEmailAdresses()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name'                  => 'Jhon',
            'email'                 => 'jhon@example.com',
            'password'              => 'foobar',
            'password_confirmation' => 'foobar',
        ]);

        $user = User::whereName('Jhon')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
             ->assertRedirect(route('threads.index'));

        $this->assertTrue($user->fresh()->confirmed);
        $this->assertNull($user->fresh()->confirmation_token);
    }

    public function testConfirmingAnInvalidToken()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
             ->assertRedirect(route('threads.index'))
             ->assertSessionHas('flash', 'Unknown token.');
    }
}
