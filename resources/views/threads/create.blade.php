@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a New Thread</div>

                <div class="card-body">
                    <form class="" action="{{ route('threads.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="channel_id">Choose a Channel: </label>
                            <select id="channel_id" class="form-control" name="channel_id" value="{{ old('channel_id') }}" required>
                                <option>Choose Cannel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->slug }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title: </label>
                            <input id="title" class="form-control" name="title" value="{{ old('title') }}" required></input>
                        </div>

                        <div class="form-group">
                            <label for="body">Body: </label>
                            <wysiwyg name="body" v-bind:value="'{{ old('body')}}'"></wysiwyg>
                        </div>

                        <button type="submit" class="btn btn-primary">Publish</button>
                    </form>
                    <div style="margin-top: 20px;">
                        @if (count($errors))
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
