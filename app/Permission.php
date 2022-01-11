<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    //
    protected $fillable = [
        'id','name','guard_name'
    ];

    protected $hidden = [
        'created_at','updated_at'
    ];
}
