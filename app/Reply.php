<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reply extends Model
{
    use Favoritable,RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner','favorites'];
    protected $appends = ['favoritesCount','isFavorited','isBest'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
            $reply->body = clean(static::wrapBody($reply->body),'thread_or_reply_body');
        });

        static::deleted(function ($reply){
            if($reply->isBest()) {
                $reply->thread->update(['best_reply_id' => null]);
            }

            $reply->thread->decrement('replies_count');
        });
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();   
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/',$this->body,$matches);

        return $matches[1];
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public static function wrapBody($body)
    {
        return  preg_replace('/@([\w\-]+)/','<a href="/profiles/$1">$0</a>',$body);
    }  

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;   
    }
}
