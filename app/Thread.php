<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
use App\Reply;
use App\User;
use App\Channel;
use App\Activity;
use App\Visits;

class Thread extends Model
{
    use RecordsActivity;

    protected $fillable = ['user_id',
                           'title',
                           'body',
                           'channel_id',
                           'slug',
                           'best_reply_id',
                           'locked'];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread){
            $thread->replies->each->delete();
        });

        static::created(function($thread){
            $thread->update(['slug' => str_slug($thread->title)]);
        });

    }

    public function path()
    {
        return '/threads/' . $this->channel->slug . '/' . $this->slug;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class)
                    ->withCount('favorites')
                    ->with('owner');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
             ->where('user_id', $userId ?: auth()->id())
             ->delete();
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                    ->where('user_id', auth()->id())
                    ->exists();
    }

    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if(static::whereSlug($slug)->exists()){
            $slug = $slug . '-' . $this->id;
        }
        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
