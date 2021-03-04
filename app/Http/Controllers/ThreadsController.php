<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Thread;
use App\Channel;
use App\Trending;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if(request()->wantsJson()){
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get(),
        ]);
    }

    public function create()
    {
        return view('threads.create');
    }

    public function store()
    {
        request()->validate([
            // 'title'         => 'required|spamfree|unique:threads',
            'title'         => 'required|spamfree',
            'body'          => 'required|spamfree',
            'channel_id'    => 'required|exists:channels,id',
        ]);

        $thread = Thread::create([
            'user_id'       => auth()->id(),
            'channel_id'    => request('channel_id'),
            'title'         => request('title'),
            'body'          => request('body'),
        ]);

        if(request()->wantsJson()){
            return response($thread, 201);
        }

        return redirect(route('threads.show', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]))
                        ->with('flash', 'Your thread has been published!');
    }

    public function show($channel, Thread $thread, Trending $trending)
    {
        if(auth()->check()){
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    public function edit(Thread $thread)
    {
        //
    }

    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->update(request()->validate([
            'title'         => 'required|spamfree',
            'body'          => 'required|spamfree',
        ]));

        return $thread;
    }

    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if(request()->wantsJson()){
            return response([], 204);
        }

        return redirect()->route('threads.index');
    }

    public function getThreads($channel, $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(15);
    }
}
