@forelse ($threads as $thread)
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <div class="level">
                <div class="flex">
                    <h4>
                      <a href="{{ route('threads.show', ['channel' => $thread->channel->slug, 'thread' => $thread->slug]) }}">
                          @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                              <strong>{{ $thread->title }}</strong>
                          @else
                              {{ $thread->title }}
                          @endif
                      </a>
                    </h4>

                    <h5>
                        Posted By:
                            <a href="{{ route('profiles.show', ['user' => $thread->creator->name]) }}">
                                {{ $thread->creator->name }}
                            </a>
                    </h5>
                </div>

                <a href="{{ route('threads.show', ['channel' => $thread->channel, 'thread' => $thread->slug]) }}">
                    {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="body">
              {!! $thread->body !!}
            </div>
        </div>
        <div class="panel-footer">
            {{ $thread->visits }} Visits
        </div>
    </div>
@empty
    <p>
        There are no relevant results at this time.
    </p>
@endforelse
