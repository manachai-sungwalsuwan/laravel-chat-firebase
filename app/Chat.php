<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'recipient_id');
    }

    public function room()
    {
        return $this->hasOne('App\Room', 'id', 'room_id');
    }
}