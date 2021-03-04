
@component('profiles.activities.activity')
    @slot('heading')
        {{ $profilesUser->name }} replied to
        <a href="{{ route('threads.show', ['channel' => $activity->subject->thread->channel, 'thread' => $activity->subject->thread->slug]) }}">
            {{ $activity->subject->thread->title }}
        </a>
    @endslot

    @slot('body')
        {!! $activity->subject->body !!}
    @endslot
@endcomponent
