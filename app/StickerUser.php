<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerUser extends Model
{
    protected $fillable = [
        'user_id', 'event_id',  'file', 'printed',
    ];

    
}
