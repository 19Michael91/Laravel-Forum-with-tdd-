<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Thread;
use App\Reply;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(3);
    }

    public function store($channel, Thread $thread, CreatePostRequest $form)
    {
        if($thread->locked){
            return response('Thread is locked', 422);
        }

        return $thread->addReply([
            'body'      => $form->get('body'),
            'user_id'   => auth()->id(),
        ])->load('owner');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if(request()->expectsJson()){
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        request()->validate([
            'body' => 'required|spamfree'
        ]);

        $reply->update(['body' => request('body')]);
    }
}
