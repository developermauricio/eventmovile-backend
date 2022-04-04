<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RateActivity extends Model
{
    protected $fillable = ['user_id', 'activity_id', 'rate'];
    protected $table = "rate_activities";
}
