<div class="card" v-if="editing">

    <div class="card-header">
        <div class="level">

            <input class="form-control" type="text" v-model="form.title">

        </div>
    </div>

    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>
    </div>

    <div class="card-footer">
        <div class="level">
            <button class="btn btn-xs btn-default level-item"
                    v-on:click="editing = true"
                    v-show="! editing">Edit</button>
            <button class="btn btn-xs btn-primary level-item"
                    v-on:click="update">Update</button>
            <button class="btn btn-xs btn-default level-item"
                    v-on:click="resetForm">Cancel</button>

            @can ('update', $thread)
                <form action="{{ route('threads.delete', [
                        'channel' => $thread->channel->slug,
                        'thread'  => $thread->slug,
                    ]) }}"
                    method="POST"
                    class="ml-a">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-danger"> Delete Thread</button>
                </form>
            @endcan
        </div>
    </div>
</div>


<div class="card" v-else>

    <div class="card-header">
        <div class="level">
            <img src="{{ $thread->creator->avatar_path }}" alt="avatar" width="30" height="30" class="mr-1">

            <span class="flex">
                <a href="{{ route('profiles.show', ['user' => $thread->creator->name]) }}">{{ $thread->creator->name }}</a> posted:
                <span v-text="title"></span>
            </span>

        </div>
    </div>

    <div class="card-body" v-html="body"></div>

    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-xs btn-default" v-on:click="editing = true">Edit</button>
    </div>
</div>
