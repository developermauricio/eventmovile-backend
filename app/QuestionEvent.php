<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class QuestionEvent extends Model
{
    protected $fillable = [
        'question', 'answer', 'type', 'event_id', 'user_id',   
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
