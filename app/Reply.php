<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $guarded = []; // чтобы без ошибки добавлялись поля
    // в методе addReply()
        
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
        // указываем user_id потому что назвали метод owner, а не user
    }
}
