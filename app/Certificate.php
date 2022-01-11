<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificate';

    protected $fillable = [
        'model_id', 'model', 'name_file'
    ];

    public function event()
    {
        return $this->hasOne('App\Event', 'model_id', 'id');
    }
}
