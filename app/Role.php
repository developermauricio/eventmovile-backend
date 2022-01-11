<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    //

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at','updated_at'
    ];
}
