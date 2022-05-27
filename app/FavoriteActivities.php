<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteActivities extends Model
{
    
    protected $fillable = ['user_id', 'activies_id'];
}
