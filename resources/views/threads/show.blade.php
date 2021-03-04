@extends('layouts.app')

@section('header')
    <link href="/css/vendor/jquery.atwho.css" rel="stylesheet">
    <script>
        window.thread = <?= json_encode($thread); ?>
    </script>
@endsection

@section('content')

<thread-view v-bind:thread="{{ $thread }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8" v-cloak>
                @include('threads._question')

                <replies @added="repliesCount++" @removed="repliesCount--"></replies>
            </div>
            <div class="col-md-4" v-cloak>
                <div class="card">
                    <div class="card-body">
                        <p>
                            This thread was published {{ $thread->created_at->diffForHumans() }} by
                            <a href="#">{{ $thread->creator->name }}</a>
                            and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                        </p>

                        <p>
                            <subscribe-button v-bind:active="{{ json_decode($thread->isSubscribedTo) ? 'true' : 'false' }}" v-if="signedIn"></subscribe-button>

                            <button type="button"
                                    class="btn btn-default"
                                    v-if="authorize('isAdmin')"
                                    v-on:click="toggleLock"
                                    v-text="locked ? 'Unlock' : 'Lock'"></button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</thread-view>
@endsection
