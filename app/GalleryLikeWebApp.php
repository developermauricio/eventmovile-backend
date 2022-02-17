<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GalleryLikeWebApp extends Model
{
    protected $fillable = ['user_id', 'gallery_id'];

    public function user(){
        return $this->hasMany(User::class, 'user_id','gallery_id');
    }

}
