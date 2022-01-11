<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProbeAnswer extends Model
{
    protected $fillable = [
        'question_id', 'probe_id', 'value', 'user_id'
    ];

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
