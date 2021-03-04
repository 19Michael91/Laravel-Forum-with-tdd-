<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use App\Notifications\YouWhereMentioned;
use App\User;

class NotifyMentionedUsers
{
    public function handle(ThreadReceivedNewReply $event)
    {
        $users = User::whereIn('name', $event->reply->mentionedUsers())
                     ->get()
                     ->each(function($user) use ($event){
                         $user->notify(new YouWhereMentioned($event->reply));
                     });
    }
}
