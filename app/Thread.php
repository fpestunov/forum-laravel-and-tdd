<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $guarded = []; // чтобы без ошибки добавлялись поля
    // в методе addReply()

    public function path()
    {
        return "/threads/" . $this->id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
        // указываем user_id, потому что он будет искать по
        // умолчанию по имени метода -  creator_id
    }

    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }
}
