
@component('profiles.activities.activity')
    @slot('heading')
        {{ $profilesUser->name }} published a
        <a href="{{ route('threads.show', ['channel' => $activity->subject->channel, 'thread' => $activity->subject->slug]) }}">
            {{$activity->subject->title}}
        </a>
    @endslot

    @slot('body')
        {!! $activity->subject->body !!}
    @endslot
@endcomponent
