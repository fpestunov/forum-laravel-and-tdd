<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // channel can have many threads
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
