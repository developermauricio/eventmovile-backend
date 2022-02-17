<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GalleryWebApp extends Model
{
    protected $fillable = [
      'user_id',
      'event_id',
      'description',
      'picture'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function galleryLike(){
        return $this->hasMany(GalleryLikeWebApp::class, 'gallery_id');
    }
}
